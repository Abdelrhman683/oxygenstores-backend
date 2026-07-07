<div class="pb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="avatar rounded avatar-70 border">
            <img class="avatar-img object-fit-cover"
                 src="{{ getStorageImages(path: $product->thumbnail_full_url, type:'backend-product') }}"
                 alt="{{ $product->name }}">
        </div>
        <div>
            <div class="h5 mb-1 line-2">{{ $product->name }}</div>
            <div class="fs-12 text-body">
                {{ translate('Total Stock') }}:
                <span class="fw-bold text-dark" id="modalTotalStockDisplay">{{ $product->current_stock }}</span>
            </div>
        </div>
    </div>
</div>

<form id="branchStockForm">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light text-capitalize">
                <tr>
                    <th>{{ translate('branch') }}</th>
                    <th class="text-center" style="width: 140px">{{ translate('quantity') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>
                            <input type="number" min="0"
                                   name="branch_stocks[{{ $branch->id }}]"
                                   value="{{ $stocks[$branch->id] ?? 0 }}"
                                   class="form-control form-control-sm branch-qty-input text-center"
                                   id="branch_qty_{{ $branch->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td class="fw-bold">{{ translate('Total') }}</td>
                    <td class="fw-bold text-center">
                        <span id="modalTotalStock">{{ $stocks->sum() }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ translate('Cancel') }}
        </button>
        <button type="submit" class="btn btn-primary" id="saveBranchStockBtn">
            <span class="spinner-border spinner-border-sm d-none me-1" id="saveBranchStockSpinner"></span>
            {{ translate('Save') }}
        </button>
    </div>
</form>

<script>
    // Live total calculation
    $(document).off('input.branchqty').on('input.branchqty', '.branch-qty-input', function () {
        let total = 0;
        $('.branch-qty-input').each(function () {
            total += parseInt($(this).val()) || 0;
        });
        $('#modalTotalStock').text(total);
    });

    // Submit handler
    $(document).off('submit.branchstock').on('submit.branchstock', '#branchStockForm', function (e) {
        e.preventDefault();
        const $btn = $('#saveBranchStockBtn');
        const $spinner = $('#saveBranchStockSpinner');
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');

        $.ajax({
            url: '{{ route("admin.products.branch-stock.update") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#branchStockModal').modal('hide');
                    // Update the stock display in the table row
                    const $row = $('[data-product-id="' + res.product_id + '"]').closest('tr');
                    $row.find('.product-stock-total').text(res.total_stock);
                    toastr.success(res.message || 'تم تحديث الستوك بنجاح');
                } else {
                    toastr.error('حدث خطأ، حاول مجدداً');
                }
            },
            error: function () {
                toastr.error('حدث خطأ في الاتصال');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
</script>
