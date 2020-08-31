<?php

use App\Quotation;
use App\User;
use Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if(!function_exists('child_assign_users'))
{
    function child_assign_users($dc, $users)
    {
        $dc_users = [];
        foreach($users as $user)
        {
            $us = User::where('name', $user)->pluck('id');
            $dc_users[] = $us[0];
        }
        $dc->users()->syncWithoutDetaching($dc_users);

        $childrens = $dc->childrens;
        foreach($childrens as $child)
        {
            child_assign_users($child, $users);
        }
    }
}

if(!function_exists('language_switcher'))
{
    function language_switcher()
    {
        $lang = app()->getLocale();

        $langs = scandir(dirname(dirname(__DIR__)) . '/public/lang');
        unset($langs[0]);
        unset($langs[1]);

        ?>

        <li class="dropdown-language">
            <a class="waves-effect waves-block waves-light translation-button" href="#" data-target="translation-dropdown" data-lang="<?php echo $lang ?>">
                <img src="<?php echo asset('lang/' . $lang . '.jpg' ); ?>" width="20px">
            </a>

            <ul class="dropdown-content" id="translation-dropdown" tabindex="0" style="">
                <?php

                foreach($langs as $other_lang)
                {
                    $lang_name = basename($other_lang, '.jpg');

                    if($lang_name == $lang)
                    {
                        continue;
                    }

                    ?>
                    <li class="dropdown-item" tabindex="0">
                        <a class="grey-text text-darken-1 lang-button" href="#" data-lang="<?php echo $lang_name ?>">
                            <img src="<?php echo asset('lang/' . $other_lang); ?>" width="24px">
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </li>

        <?php
    }
}

if(!function_exists('generate_sequential'))
{
    function generate_sequential()
    {
        $first_date = date('Y-m-d H:i:s', strtotime('first day of December this year'));
        if(date('Y-m-d H:i:s') < $first_date)
        {
            $first_date = date('Y-m-d H:i:s', strtotime('first day of December last year'));
        }

        $quotations = DB::table('quotation')->where('created_at', '>',  $first_date)->max('sequential_number');

        if(is_null($quotations))
        {
            return 1;
        }
        else
        {
            return $quotations+1;
        }
    }
}

if(!function_exists('get_code'))
{
    function get_code($quotation)
    {
        $check_date = date('Y-m-d H:i:s', strtotime('first day of December this year'));
        $date = date('y', strtotime('last year'));
        if(date('Y-m-d H:i:s') < $check_date)
        {
            $date = date('y');
        }
        $company_code = $quotation->company->code;

        return $date . $company_code . $quotation->sequential_number;
    }
}

if(!function_exists('get_comment_childrens'))
{
    function get_comment_childrens($comment)
    {
        foreach($comment->childrens as $children)
        {
            ?>
            <div class="card">
                <div class="card-content">
                    <div class="row" name="comment_row">
                        <div class="col s3 l1" style="width: auto">
                            <?php if(!is_null($children->user->user_img)) : ?>
                                <div class="circle" style="background-image: url('<?php echo Storage::url("users/") . $children->user->user_img ?>'); height: 50px; width: 50px; background-position: center;background-size: cover; background-repeat: no-repeat;"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col s8 l11 left-align" style="padding-left: 0">
                            <span class="black-text"> <?php echo $children->user->name ?> </span> <br>
                            <p class="mt-4"><?php echo $children->comment ?></p>
                        </div>
                    </div>
                    <div class="row valign-wrapper">
                        <div class="col s12 mt-3 ml-6" style="padding-left: 0">
                            <?php if(Auth::user()->roles->first()->id < 4) : ?>
                                <button name="edit_comment" data-id="<?php echo $children->id ?>" class="btn btn-small waves-effect waves-light m-1" title="<?php echo __('Edit') ?>"> <?php echo __('Edit') ?> </button>
                            <?php endif; ?>
                            <?php if(Auth::user()->roles->first()->id < 5) : ?>
                                <button name="reply_comment" data-id="<?php echo $children->id ?>" class="btn btn-small waves-effect waves-light m-1" title="<?php echo __('Reply') ?>"> <?php echo __('Reply') ?> </button>
                            <?php endif; ?>
                            <?php if(Auth::user()->roles->first()->id < 4 || Auth::user()->id == $comment->user->id) : ?>
                                <button name="remove_comment" data-id="<?php echo $children->id ?>" class="btn btn-small waves-effect waves-light red m-1" title="<?php echo __('Remove') ?>"> <?php echo __('Remove') ?> </button>
                            <?php endif; ?>
                            <?php get_comment_childrens($children); ?>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }
    }
}


if(!function_exists('quotation_change'))
{
    function quotation_change($change)
    {
        //$old_value = '';
        //$new_value = '';

        $old_value = $change->old_value;
        $new_value = $change->new_value;
        ?>
        <ul>
            <li class="my-1"><b class="black-text"><?php echo __('From') ?>:</b> <?php echo $old_value ?> </li>
            <li class="my-1"><b class="black-text"><?php echo __('To') ?>:</b> <?php echo $new_value ?> </li>
        </ul>
        <?php
    }
}

if(!function_exists('remove_childs_comments'))
{
    function remove_childs_comments($parent_id)
    {
        $comments = DB::table('comments')->where('comment_id', $parent_id)->get();
        if(count($comments) > 0)
        {
            foreach($comments as $comment)
            {
                remove_childs_comments($comment->id);
            }
        }
        DB::table('comments')->where('id', $parent_id)->delete();
    }
}
