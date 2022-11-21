<section class="list list--reviews" id="reviews">
    <div class="list--reviews__wrapper position-relative">
            <div class="position-absolute r-0 t--1-2">
                <a href="#review-modal-component" class="btn btn-highlight add-a-review"  data-toggle="modal" data-target="#review-modal-component">Add a review</a>
            </div>
            <h3 class="list--reviews__headline mb-3">reviews (<span class="review-count">{{ count($reviews) }}</span>)</h3>
            <div class="list--reviews__list">
                @if (!$reviews->isEmpty())
                @foreach ($reviews->sortByDesc('created_at') as $review)
                    @include('front.reviews.list-item')
                @endforeach
                @else
                <h4 class="text-highlight h5">No reviews yet</h4>
                <div class="d-none">
                    @include('front.reviews.list-item')
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="modal fade" tabindex="-1" data-keyboard="true" role="dialog" aria-hidden="true" id="review-modal-component" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content container">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    Add a review
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('front.reviews.form', ['product' => $product])
            </div>
        </div>
    </div>
</div>
