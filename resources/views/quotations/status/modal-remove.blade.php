<div class="modal fade" id="remove_modal" tabindex="-1" role="dialog" aria-labelledby="remove_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remove_title"> @lang('Remove Quotation Status') </h5>
            </div>
            <div class="modal-body">
                <span>
                    @lang('Are you sure you want to remove the quotation status?')
                </span>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="quotation_status_id" value="">
                <a href="#" class="modal-close btn waves-effect waves-light"> @lang('Cancel') </a>
                <button type="button" id="confirm_remove" data-model="status" data-type="quotation_status" class="btn waves-effect waves-light red"> @lang('Remove') </button>
            </div>
        </div>
    </div>
</div>
