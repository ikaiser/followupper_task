<div class="modal fade" id="company_remove_modal" tabindex="-1" role="dialog" aria-labelledby="remove_company_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remove_company_title"> @lang('Remove Company') </h5>
            </div>
            <div class="modal-body">
                <span>
                    @lang('Are you sure you want to remove this company?')
                </span>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="company_id" value="">
                <a href="#" class="modal-close btn waves-effect waves-light"> @lang('Cancel') </a>
                <button type="button" id="confirm_company_remove" class="btn waves-effect waves-light red"> @lang('Remove') </button>
            </div>
        </div>
    </div>
</div>
