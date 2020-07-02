<div class="modal fade" id="dc_remove_modal" tabindex="-1" role="dialog" aria-labelledby="remove_dc_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remove_dc_title"> @lang('Remove Room') </h5>
            </div>
            <div class="modal-body">
                <span>
                    <p id="modal-p" class="my-0" data-text="@lang('Are you sure you want to remove the room')"> @lang('Are you sure you want to remove the room') </p>
                    <p class="my-0"> @lang('The rooms and the contents inside will be removed.') </p>
                </span>

            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close btn waves-effect waves-light"> @lang('Cancel') </a>
                <button type="button" id="confirm_remove" class="btn waves-effect waves-light red"> @lang('Remove') </button>
            </div>
        </div>
    </div>
</div>
