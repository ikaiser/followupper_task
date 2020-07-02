<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Datacuration;
use App\DatacurationElement;
use App\Mail\NewDocument;
use App\Project;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpParser\Builder\Declaration;

class DataCurationElementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, $dc_id = '')
    {
        $project = Project::find($id);

        $rooms = Datacuration::where('project_id', $id)->whereNull('parent_dc')->get();;

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

        return view('datacuration_element.create', ['project' => $project, 'parent' => $dc_id, 'rooms' => $rooms, 'dcs' => $dcs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $project_id)
    {
        $request->validate([
            'file'      => 'required',
            'thumbnail' => 'image',
            'rooms'     => 'array|required',
            'tags'      => 'array',
        ]);

        $project = Project::find($project_id);

        $user = Auth::user();
        $dce = new DatacurationElement;
        $dce->name          = $request->file->getClientOriginalName();
        $dce->description   = is_null($request->get('description')) ? '' : $request->get('description');
        $dce->topic         = is_null($request->get('topic')) ? '' : $request->get('topic');
        $dce->user_id       = $user->getAuthIdentifier();
        $dce->project_id    = $project_id;
        $dce->extension     = $request->file->getClientOriginalExtension();
        $dce->send_email    = is_null($request->get('send_email')) ? 0 : $request->get('send_email');
        $dce->save();

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

                $new_tag->dce()->attach($dce->id);
            }
            else
            {
                $dt_tag->dce()->attach($dce->id);
            }
        }

        if(!is_null($request->thumbnail))
        {
            $thumbnail = 'th_' . $dce->id . '.' . $request->thumbnail->getClientOriginalExtension();

            $dce->thumbnail = $thumbnail;
            $dce->save();

            $request->file('thumbnail')->storeAs("public/project/{$project_id}/files/thumbnails/", $thumbnail);
        }

        foreach($request->get('rooms') as $room)
        {
            $dce->dc()->attach($room);
        }

        $request->file('file')->storeAs("public/project/{$project_id}/files/", $request->file->getClientOriginalName());

        return redirect()->route('dc.index', $project)->with('message', __('File Added Successfully!') );
    }

    /**
     * Display the specified resource.
     *
     * @param $project
     * @param $file_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($project, $file_id)
    {
        $project = Project::find($project);
        $file = DatacurationElement::find($file_id);
        $comments = Comment::with('childrens')->doesntHave('parent')->where('file_id', $file_id)->orderBy('id', 'desc')->get();
        $rooms = $file->dc()->pluck('name');

        if(is_null($file))
        {
            return redirect()->route('projects.index')->with('error', __('File not Existing') );
        }

        return view('datacuration_element.show', ['file' => $file, 'project' => $project, 'comments' => $comments, 'rooms' => $rooms]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($project, $file_id)
    {
        $project = Project::find($project);
        $file = DatacurationElement::with('dc')->find($file_id);
        $rooms = Datacuration::where('project_id', $project->id)->whereNull('parent_dc')->get();;

        $room_ids = DB::table('datacuration_element_datacuration')
            ->where('datacuration_element_datacuration.datacuration_element_id', $file_id)
            ->where('datacuration_element_datacuration.datacuration_id', 0)
            ->select('datacuration_element_datacuration.datacuration_id')->distinct()->get();

        $file_rooms = array();
        foreach($file->dc as $dc)
        {
            $file_rooms[$dc->getLevel()]['checked'] = true;
            $file_rooms[$dc->getLevel()]['rooms'][] = $dc;
            if($dc->getLevel() == '1' && $room_ids->count() > 0)
            {
                $file_rooms[$dc->getLevel()]['room_project'] = true;
            }
        }
        ksort($file_rooms);

        foreach($file_rooms as $key => $file_room)
        {
            $file_rooms[$key]['rooms'] = collect($file_room['rooms']);
        }

        $levels = array_keys($file_rooms);
        sort($levels);

        for($i = end($levels); $i > 1; $i--)
        {
            if(!isset($file_rooms[$i]))
            {
                $upper = $i + 1;
                $upper_parents = array();
                foreach($file_rooms[$upper]['rooms'] as $room)
                {
                    $upper_parents[] = $room->parent_dc;
                }
                $upper_parents = array_unique($upper_parents);

                $level_rooms = Datacuration::whereIn('id', $upper_parents)->get();

                $file_rooms[$i]['checked'] = false;
                $file_rooms[$i]['rooms'] = $level_rooms;
            }
        }
        ksort($file_rooms);

        $select_rooms = array();
        $room_names = array();
        foreach($file_rooms as $key => $file_level)
        {

            if($key == 1)
            {
                $select_rooms[$key] = $rooms->sortBy('name', SORT_STRING|SORT_FLAG_CASE);
            }
            elseif($key == 2)
            {
                $ids = $file_rooms[$key-1]['rooms']->pluck('id');

                $select_level_rooms = Datacuration::whereIn('parent_dc', $ids)->get();
                $select_level_rooms = $select_level_rooms->map(function ($item, $key)
                {
                    $item->name = $item->parent->name . ' / ' . $item->name;
                    return $item;
                });
                $room_names = $select_level_rooms->pluck('name', 'id')->toArray();
                $select_rooms[2] = $select_level_rooms->sortBy('name', SORT_STRING|SORT_FLAG_CASE);
            }
            else
            {
                $ids = $file_rooms[$key-1]['rooms']->pluck('id');
                $select_level_rooms = Datacuration::whereIn('parent_dc', $ids)->get();
                $select_level_rooms = $select_level_rooms->map(function ($item, $key) use ($room_names)
                {
                    $item->name = $room_names[$item->parent_dc] . ' / ' . $item->name;
                    return $item;
                });

                $room_names = $select_level_rooms->pluck('name', 'id')->toArray();

                $select_rooms[$key] = $select_level_rooms->sortBy('name', SORT_STRING|SORT_FLAG_CASE);
            }
        }

        if(is_null($file))
        {
            return redirect()->route('projects.index')->with('error', __('File not Existing') );
        }

        $tags = $file->tags()->get();

        return view('datacuration_element.edit', ['file' => $file, 'project' => $project, 'rooms' => $rooms, 'file_rooms' => $file_rooms, 'select_rooms' => $select_rooms, 'tags' => $tags]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $project, $file_id)
    {
        $request->validate([
            'file'      => 'file',
            'thumbnail' => 'image',
            'rooms'     => 'array|required',
            'tags'      => 'array',
        ]);

        $file = DatacurationElement::find($file_id);

        $user = Auth::user();

        $file->description   = is_null($request->get('description')) ? '' : $request->get('description');
        $file->topic         = is_null($request->get('topic')) ? '' : $request->get('topic');
        $file->send_email    = is_null($request->get('send_email')) ? 0 : $request->get('send_email');
        $file->user_id       = $user->getAuthIdentifier();
        $file->save();

        $file->tags()->detach();

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

                $new_tag->dce()->attach($file_id);
            }
            else
            {
                $dt_tag->dce()->attach($file_id);
            }
        }

        if(!is_null($request->thumbnail))
        {
            $thumbnail = 'th_' . $file->id . '.' . $request->thumbnail->getClientOriginalExtension();

            $file->thumbnail = $thumbnail;
            $file->save();

            $request->file('thumbnail')->storeAs("public/project/{$project}/files/thumbnails/", $thumbnail);
        }
        if(!is_null($request->file))
        {
            Storage::delete("public/project/{$project}/files/" . $file->name);

            $file->name = $request->file->getClientOriginalName();
            $file->extension = $request->file->getClientOriginalExtension();
            $request->file('file')->storeAs("public/project/{$project}/files/", $request->file->getClientOriginalName());
            $file->save();

        }

        $file->dc()->detach();
        foreach($request->get('rooms') as $room)
        {
            $file->dc()->attach($room);
        }

        return redirect()->route('dce.show', [$project, $file->id])->with('message', __('File Updated Successfully!') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $project_id
     * @param $file_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($project_id, $file_id)
    {
        $file = DatacurationElement::find($file_id);
        if(is_null($file))
        {
            return redirect()->route('dc.index', $project_id)->with('error', __('File Already Deleted or not Existing') );
        }

        Storage::delete("public/project/{$project_id}/files/" . $file->name);
        Storage::delete("public/project/{$project_id}/files/thumbnails/" . $file->thumbnail);
        $file->tags()->detach();
        $file->delete();

        return redirect()->route('dc.index', $project_id)->with('message', __('File Deleted Successfully!') );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $text       = $request->get('search_text');
        $rel        = $request->get('rel');
        $projects   = $request->get('projects');
        $tags       = $request->get('tags');
        $doc_type   = $request->get('doc_type');
        $authors    = $request->get('authors');
        $start_date = $request->get('start_date');
        $end_date   = $request->get('end_date');

        $req = array(
            'text'      => $text,
            'rel'       => $rel,
            'project'   => $projects,
            'tags'      => $tags,
            'doc_type'  => $doc_type,
            'authors'   => $authors,
            'start_date'=> $start_date,
            'end_date'  => $end_date,
        );

        $file_search = DB::table('datacuration_element');
        $file_search->leftjoin('dce_tags', 'datacuration_element.id', '=', 'dce_tags.datacuration_element_id');
        $file_search->leftjoin('tags', 'dce_tags.tag_id', '=', 'tags.id');
        $file_search->leftjoin('comments', 'comments.file_id', '=', 'datacuration_element.id');

        foreach($text as $key => $string)
        {
            if($key == 0)
            {
                if(!is_null($string))
                {
                    $file_search->Where(function ($query) use ($string)
                    {
                        $query->orWhere('datacuration_element.name', 'LIKE', "%{$string}%")->orWhere('datacuration_element.description', 'LIKE', "%{$string}%")->orWhere('datacuration_element.topic', 'LIKE', "%{$string}%")->orWhere('comments.comment', 'LIKE', "%{$string}%");
                    });
                }
            }
            else
            {
                if(!is_null($string))
                {
                    $relation = $rel[$key - 1];
                    if($relation == 'and')
                    {
                        $file_search->Where(function ($query) use ($string)
                        {
                            $query->orWhere('datacuration_element.name', 'LIKE', "%{$string}%")->orWhere('datacuration_element.description', 'LIKE', "%{$string}%")->orWhere('datacuration_element.topic', 'LIKE', "%{$string}%")->orWhere('comments.comment', 'LIKE', "%{$string}%");
                        });
                    } else
                    {
                        $file_search->orWhere(function ($query) use ($string)
                        {
                            $query->orWhere('datacuration_element.name', 'LIKE', "%{$string}%")->orWhere('datacuration_element.description', 'LIKE', "%{$string}%")->orWhere('datacuration_element.topic', 'LIKE', "%{$string}%")->orWhere('comments.comment', 'LIKE', "%{$string}%");
                        });
                    }
                }
            }
        }
        //Progetto

        if(!empty($projects))
        {
            $projects = explode(';', $projects);
            $file_search->WhereIn('datacuration_element.project_id', $projects);
        }

        //Tag
        if(!empty($tags))
        {
            if($tags[0] != null)
            {
                $file_search->WhereIn('tags.tag', $tags);
            }
        }

        //Estensione File
        if(!is_null($doc_type))
        {
            $file_search->Where('datacuration_element.extension', $doc_type);
        }

        //Autore
        if(!is_null($authors))
        {
            $file_search->WhereIn('datacuration_element.user_id', $authors);
        }

        //Data Inizio
        if(!is_null($start_date))
        {
            $file_search->whereDate('datacuration_element.created_at', '>=', $start_date);
        }

        //Data Fine
        if(!is_null($end_date))
        {
            $file_search->whereDate('datacuration_element.created_at', '<=', $end_date);
        }

        $file_search->select('datacuration_element.id')->distinct();

        $file_search_res = $file_search->get();

        $file_ids_array = [];
        foreach($file_search_res as $file)
        {
            $file_ids_array[] = $file->id;
        }
        $files = DatacurationElement::whereIn('id', $file_ids_array)->get();

        $project = array();
        if(!is_null($projects) && count($projects) == 1)
        {
            $project = Project::find($projects[0]);
        }

        if(isset($_GET['layout']) && $_GET['layout'] == 'table')
        {
            return view('projects.results_table', ['files' => $files, 'req' => $req, 'project' => $project]);
        }
        return view('projects.results', ['files' => $files, 'req' => $req, 'project' => $project]);
    }

    function fetch_extension(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $data = DatacurationElement::select('extension')->Where('extension', 'LIKE', "%{$query}%")->groupBy('extension')->orderByRaw('extension ASC')->get();
            $output = '<ul class="collection"  style="display:block; position:relative">';
            foreach($data as $row)
            {
                $output .= '<li class="collection-item" data-ref="doc_type"><a data-value="' . $row->extension . '" href="#">' . $row->extension . '</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function fetch_tags(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');

            $data = Tag::where('tag', 'LIKE', "%{$query}%")->orderByRaw('tag ASC')->get();

            //$data = DatacurationElement::select('tags')->Where('tags', 'LIKE', "%{$query}%")->groupBy('tags')->orderByRaw('tags ASC')->get();
            $output = '<ul class="collection" style="display:block; position:relative">';
            foreach($data as $row)
            {
                $output .= '<li class="collection-item" data-ref="tags"><a data-value="' . $row->tag . '" href="#">' . $row->tag . '</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    /**
     * @return array
     */
    function get_rooms()
    {
        $parent_rooms = isset($_POST['rooms']) ? $_POST['rooms'] : array();

        $parent_rooms_array = isset($_POST['rooms_array']) ? $_POST['rooms_array'] : array();
        unset($parent_rooms_array[0]);

        if(empty($parent_rooms))
        {
            $data = array(
                'result' => 'empty'
            );

            return $data;
        }

        if(in_array(0, $parent_rooms))
        {
            $key = array_search(0, $parent_rooms);
            unset($parent_rooms[$key]);
        }

        $rooms = Datacuration::with('parent')->wherein('parent_dc', $parent_rooms)->get();


        foreach($rooms as $room)
        {
            $room->name = $parent_rooms_array[$room->parent_dc] . ' / ' . $room->name;
        }

        $data = array(
            'result' => 'OK',
            'data' => $rooms,
            'lang'  => __('Choose a Room')
        );

        return $data;
    }
}
