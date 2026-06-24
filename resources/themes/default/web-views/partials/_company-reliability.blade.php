@if(count($companyReliability) > 0)
<div class="container rtl pb-4 custom_pd">
    <div class="benefits-section">
        <div class="benefits-header">
            <h4 class="benefits-title">لماذا متجرنا</h4>
        </div>
        
        <div class="benefits-grid">
            @foreach ($companyReliability as $key=>$value)
                @if ($value['status'] == 1 && !empty($value['title']))
                    <div class="benefits-item">
                        <div class="benefits-icon-wrapper">
                            <img loading="lazy" alt="" class="object-contain" width="250" height="250" src="{{ getStorageImages(path: imagePathProcessing(imageData: $value['image'],path: 'company-reliability'), type: 'source', source: 'public/assets/front-end/img'.'/'.$value['item'].'.png') }}">
                        </div>
                        {{-- 
                        <div class="benefits-text">
                            <p class="m-0">{{ $value['title'] }}</p>
                        </div>
                        --}}
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
