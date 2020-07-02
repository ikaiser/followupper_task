<?php

namespace App\Http\Controllers;

use App\Datacuration;
use App\DatacurationElement;
use App\Mail\NewRoom;
use App\Project;
use App\Role;
use App\Tag;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\VarDumper\Cloner\Data;

class DataCurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index($id)
    {
        $project = Project::find($id);
        $user = Auth::user();
        $role = Auth::user()->roles->first();

        if($role->id == 4)
        {
            $dcs = $user->dcs()->where('project_id', $id)->whereNull('parent_dc');
        }
        else
        {
            $dcs = Datacuration::where('project_id', $id)->whereNull('parent_dc');
        }

        $file_ids = DB::table('datacuration_element')
            ->join('datacuration_element_datacuration', 'datacuration_element.id', '=', 'datacuration_element_datacuration.datacuration_element_id')
            ->where('datacuration_element_datacuration.datacuration_id', 0)
            ->select('datacuration_element.id')->distinct()->get();

        $file_ids_array = [];
        foreach($file_ids as $file_id)
        {
            $file_ids_array[] = $file_id->id;
        }

        $files = DatacurationElement::whereIn('id', $file_ids_array)->where('project_id', $id);

        if(Session::has('sort'))
        {
            $dcs = $dcs->orderBy(Session::get('sort'), 'asc');
            $files = $files->orderBy(Session::get('sort'), 'asc');
        }

        $dcs = $dcs->get();
        $files = $files->get();

        $dest = 'datacuration.index';
        if(isset($_GET['layout']) && $_GET['layout'] == 'table')
        {
            $dest = 'datacuration.index_table';
        }

        return view($dest, ['project' => $project, 'dcs' => $dcs, 'files' => $files]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $id
     * @return Factory|View
     */
    public function create($id, $dc_id = '')
    {
        $project = Project::find($id);
        $dcs= [];
        if(!empty($dc_id))
        {
            $dc = Datacuration::find($dc_id);
            $pdc = $dc;
            $dcs[] = $dc;
            while($pdc->parent_dc != null)
            {
                $pdc = Datacuration::find($pdc->parent_dc);
                $dcs[] = $pdc;
            }
            $dcs = array_reverse($dcs);
        }

        return view('datacuration.create', ['project' => $project, 'parent' => $dc_id, 'dcs' => $dcs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name'  => 'required|max:255',
            'file'  => 'required|image',
            'tags'  => 'array',
        ];

        $customMessages = [
            'file.required' => __('The thumbnail is required.'),
            'file.image' => __('The thumbnail must be an image.')
        ];

        $request->validate($rules, $customMessages);

        $project = Project::find($request->get('project'));
        $dc = new Datacuration;

        $dc->name       =   $request->get('name');
        $dc->project_id =   $request->get('project');
        $dc->parent_dc  =   $request->get('parent_dc');
        $dc->send_email =   is_null($request->get('send_email')) ? 0 : $request->get('send_email');
        $dc->save();

        if(!empty($request->get('parent_dc')))
        {
            $parent = Datacuration::find($request->get('parent_dc'));
            $users = $parent->users;

            foreach($users as $user)
            {
                $user->dcs()->attach($dc->id);
            }
        }
        else
        {
            $project = Project::find($request->get('project'));
            $users = $project->users()->whereHas('roles', function ($q) {$q->where('id', 4);})->get();

            foreach($users as $user)
            {
                $user->dcs()->attach($dc->id);
            }
        }

        foreach($request->get('tags') as $tag)
        {
            if(empty($tag))
            {
                continue;
            }
            $dt_tag = Tag::where('tag', $tag)->first();
            if(is_null($dt_tag))
            {
                $new_tag = new Tag;
                $new_tag->tag = $tag;
                $new_tag->save();

                $new_tag->dc()->attach($dc->id);
            }
            else
            {
                $dt_tag->dc()->attach($dc->id);
            }
        }

        $thumbnail = 'dc_' . $dc->id . '.' . $request->file->getClientOriginalExtension();
        $dc->thumbnail = $thumbnail;
        $dc->save();

        $request->file('file')->storeAs('public/dc', $thumbnail);

        /*
        foreach($users as $user)
        {
            Mail::to($user)->send(New NewRoom($user, $dc));
        }
        */
        return redirect()->route('dc.get', [$project, $request->get('parent_dc')])->with('message', __('Room Created Successfully!') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Factory|View
     */
    public function get($project_id, $id)
    {
        $project = Project::find($project_id);
        $user = Auth::user();
        $role = Auth::user()->roles->first();

        if($role->id == 4)
        {
            $rooms = $user->dcs()->where('parent_dc', $id);
        }
        else
        {
            $rooms = Datacuration::where('parent_dc', $id);
        }

        $dc = Datacuration::find($id);

        $pdc = $dc;
        $dcs = [];
        $dcs[] = $dc;
        while($pdc->parent_dc != null)
        {
            $pdc = Datacuration::find($pdc->parent_dc);
            $dcs[] = $pdc;
        }
        $dcs = array_reverse($dcs);

        $files = DatacurationElement::whereHas('dc', function ($q) use($id) {
            $q->where('id', $id);
        })->where('project_id', $project_id);

        if(Session::has('sort'))
        {
            $rooms = $rooms->orderBy(Session::get('sort'), 'asc');
            $files = $files->orderBy(Session::get('sort'), 'asc');
        }

        $rooms = $rooms->get();
        $files = $files->get();

        $dest = 'datacuration.room';
        if(isset($_GET['layout']) && $_GET['layout'] == 'table')
        {
            $dest = 'datacuration.room_table';
        }

        return view($dest, ['project' => $project, 'rooms' => $rooms, 'dc' => $dc, 'files' => $files, 'dcs' => $dcs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $project_id
     * @param int $id
     * @return Factory|RedirectResponse|View
     */
    public function edit($project_id, $id)
    {
        $dc = Datacuration::find($id);
        if(is_null($dc))
        {
            return redirect()->route('projects.index')->with('error', __('Room not Existing') );
        }

        $pdc = $dc;
        $dcs = [];
        $dcs[] = $dc;
        while($pdc->parent_dc != null)
        {
            $pdc = Datacuration::find($pdc->parent_dc);
            $dcs[] = $pdc;
        }
        $dcs = array_reverse($dcs);

        $tags = $dc->tags()->get();
        return view('datacuration.edit', ['dc' => $dc, 'tags' => $tags, 'dcs' => $dcs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'  => 'required|max:255',
            'file'  => 'image',
            'tags'  => 'array',
        ];

        $customMessages = [
            'file.image' => __('The thumbnail must be an image.')
        ];

        $request->validate($rules, $customMessages);

        $dc = Datacuration::find($id);
        if(is_null($dc))
        {
            return redirect()->route('projects.index')->with('error', __('Room not Existing') );
        }

        $dc->name = $request->get('name');
        $dc->send_email = is_null($request->get('send_email')) ? 0 : $request->get('send_email');

        $dc->tags()->detach();

        foreach($request->get('tags') as $tag)
        {
            if(empty($tag))
            {
                continue;
            }
            $dt_tag = Tag::where('tag', $tag)->first();
            if(is_null($dt_tag))
            {
                $new_tag = new Tag;
                $new_tag->tag = $tag;
                $new_tag->save();

                $new_tag->dc()->attach($id);
            }
            else
            {
                $dt_tag->dc()->attach($id);
            }
        }

        if(!is_null($request->file))
        {
            Storage::delete('public/dc/' . $dc->thumbnail);

            $thumbnail = 'dc_' . $dc->id . '.' . $request->file->getClientOriginalExtension();
            $dc->thumbnail = $thumbnail;
            $request->file('file')->storeAs('public/dc', $thumbnail);
        }
        $dc->save();

        $project = Project::find($dc->project_id);
        return redirect()->route('dc.get', ['project' => $project, 'dc' => $dc->id, ])->with('message', __('Room Updated Successfully!') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $project_id
     * @param  $id
     * @return RedirectResponse
     */
    public function destroy($project_id, $id)
    {
        $project = Project::find($project_id);
        $dc = Datacuration::find($id);
        if(is_null($dc))
        {
            return redirect()->route('dc.index', $project_id)->with('error', __('Room Already Deleted or not Existing') );
        }
        $room = $dc->parent_dc;
        $thumbnail = $dc->thumbnail;

        if($dc->childrens()->exists())
        {
            return redirect()->route('dc.index', $project_id)->with('error', __('You can\'t remove a room that has rooms inside.') );
        }

        if($project_id != $dc->project_id)
        {
            return redirect()->route('dc.index', $project_id)->with('error', __('You can\'t remove the room of another project.') );
        }

        Storage::delete('public/dc/' . $thumbnail);
        $dc->tags()->detach();
        $dc->delete();

        if(!is_null($room))
        {
            return redirect()->route('dc.get', [$project, $room])->with('message', __('Room Deleted Successfully!') );
        }
        else
        {
            return redirect()->route('dc.index', $project)->with('message', __('Room Deleted Successfully!') );
        }
    }

    /**
     * @param $project_id
     * @param $id
     * @return Factory|View
     */
    public function assign_users($project_id, $id)
    {
        $project = Project::find($project_id);
        $dc = Datacuration::find($id);
        $users = User::whereHas('roles', function($q) {
            $q->where('id', 4);
        })->get();

        $pdc = $dc;
        $dcs = [];
        $dcs[] = $dc;
        while($pdc->parent_dc != null)
        {
            $pdc = Datacuration::find($pdc->parent_dc);
            $dcs[] = $pdc;
        }
        $dcs = array_reverse($dcs);

        return view('datacuration.assign', ['dc' => $dc, 'users' => $users, 'project' => $project, 'dcs' => $dcs]);
    }

    /**
     * @param Request $request
     * @param $project_id
     * @param $dc_id
     * @return \Illuminate\Http\RedirectResponse
     */
    function save_users(Request $request, $project_id, $dc_id)
    {
        $project = Project::find($project_id);
        $dc = Datacuration::find($dc_id);
        $dc->users()->detach();

        $parent_dc = $dc->parent;

        $users = $request->get('users');
        $users = array_unique($users);

        foreach($users as $key => $user)
        {
            if(is_null($user))
            {
                unset($users[$key]);
                continue;
            }

            $project_user = User::where('name', $user)->get()->first();
            $project_user->dcs()->attach($dc_id);
        }

        $childrens = $dc->childrens;
        foreach($childrens as $child)
        {
            child_assign_users($child, $users);
        }

        if(!is_null($parent_dc))
        {
            return redirect()->route('dc.get', [$project, $parent_dc->id])->with('message', __('Users Assigned Successfully to the Room!') );
        }
        else
        {
            return redirect()->route('dc.index', [$project])->with('message', __('Users Assigned Successfully to the Room!') );
        }
    }

    /**
     *
     */
    function sort()
    {
        $project_id = $_POST['project'];
        $room = $_POST['room'];
        $sort = $_POST['sort'];

        if($sort == 'date')
        {
            $sort = 'created_at';
        }

        $project = Project::find($project_id);
        $user = Auth::user();
        $role = Auth::user()->roles->first();

        if($role->id == 4)
        {
            $dcs = $user->dcs()->where('project_id', $project_id);
        }
        else
        {
            $dcs = Datacuration::where('project_id', $project_id);
        }

        if($room == 0)
        {
            $dcs->whereNull('parent_dc');

            $file_ids = DB::table('datacuration_element')
                ->join('datacuration_element_datacuration', 'datacuration_element.id', '=', 'datacuration_element_datacuration.datacuration_element_id')
                ->where('datacuration_element_datacuration.datacuration_id', 0)
                ->select('datacuration_element.id')->distinct()->get();

            $file_ids_array = [];
            foreach($file_ids as $file_id)
            {
                $file_ids_array[] = $file_id->id;
            }

            $files = DatacurationElement::whereIn('id', $file_ids_array)->where('project_id', $project_id);
        }
        else
        {
            $dcs->where('parent_dc', $room);

            $files = DatacurationElement::whereHas('dc', function ($q) use($room) {
                $q->where('id', $room);
            })->where('project_id', $project_id);
        }

        $rooms = $dcs->orderBy($sort, 'asc')->get();
        $files = $files->orderBy($sort, 'asc')->get();

        Session::put('sort', $sort);

        echo '<div class="row" id="dc_row">';
        foreach($rooms as $room)
        {
            ?>
            <div class="col s12 l4 m4">
                <div class="card mb-4 hoverable">
                    <div style="background-image: url('<?php echo Storage::url('dc/') . $room->thumbnail ?>'); height: 150px; width: 100%; background-position: center;background-size: cover; background-repeat: no-repeat;"></div>
                    <div class="card-content">
                        <span class="card-title truncate"><?php echo $room->name ?></span>
                        <div class="mt-2">
                            <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='<?php echo route('dc.get', [$project->id, $room->id]) ?>'"> <?php echo __('View') ?> </button>
                            <?php if($role->id < 4) : ?>
                                <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='<?php echo route('dc.users', [$project->id, $room->id]) ?>'"> <?php echo __('Assign Users') ?> </button>
                                <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='<?php echo route('dc.edit', [$project->id, $room->id]) ?>'"> <?php echo __('Edit') ?> </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';

        echo '<span class="card-title">' . __('File') . '</span>';
        echo '<div class="row">';
        foreach($files as $file)
        {
            ?>
            <div class="col s12 l4 m4">
                <div class="card-panel border-radius-6 mt-10 card-animation-1" style="height: 320px">

                    <?php if(!is_null($file->thumbnail)) : ?>
                        <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="<?php echo Storage::url("project/{$project_id}/files/thumbnails/") . $file->thumbnail ?>">
                    <?php else : ?>
                        <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="https://via.placeholder.com/700x200?text=<?php echo urlencode($file->name) ?>">
                    <?php endif; ?>
                    <div class="card-content">
                        <p class="truncate" style="font-weight: bold" title="<?php echo $file->description ?>"> <?php echo $file->description ?> </p>
                        <p><a href=" <?php echo route('dce.show', [$project_id, $file->id]) ?>" class="mt-5 truncate" title="<?php echo $file->name ?>"> <?php echo $file->name ?> </a></p>
                        <div class="row mt-3">
                            <div class="col s6 p-0 mt-2 left-align">
                                <span class="pt-2"> <?php echo __('Uploaded:') ?> <?php echo date('d/m/Y', strtotime($file->created_at)) ?> </span>
                            </div>
                            <div class="col s6 mt-1 right-align">
                                <span class="material-icons light-blue-text lighten-2 ml-10">chat_bubble_outline</span>
                                <span class="ml-3 vertical-align-top"> <?php echo $file->comments->count() ?> </span>
                                <?php if($role->id < 4) : ?>
                                    <span class="material-icons light-blue-text lighten-2 ml-10 pointer" onclick="document.location.href='<?php echo route('dce.edit', [$project_id, $file->id]) ?>'" >edit</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    }
}
