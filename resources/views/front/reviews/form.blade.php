<div class="add-review">
    <form method="POST" action="{{ route('product.review', $product) }}" class="form--add-review">
        {!! csrf_field() !!}
        <div class="fieldset">
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <label for="create-rating-form-nickname" class="pb-3 col-12 px-0">
                <span class="required">Name</span>
                <input required aria-required="true" type="text" name="name" id="create-rating-form-nickname" class="form-control formatted-input">
            </label>
            <label for="create-rating-form-nickname" class="pb-3 col-12 px-0">
                <span class="required">Email</span>
                <input required aria-required="true" type="email" name="email_address" id="create-rating-form-email_address" class="form-control formatted-input">
            </label>
            <label for="create-rating-form-summary" class="pb-3 col-12 px-0">
                <span class="required">Summary of your review</span>
                <input required aria-required="true" type="text" name="title" id="create-rating-form-title" class="form-control formatted-input">
            </label>
            <label for="create-rating-form-review-content" class="pb-3 col-12 px-0">
                <span class="required">Review</span>
                <textarea class="form-control formatted-input" required aria-required="true" name="description" id="create-rating-form-review-description" cols="30" rows="5"></textarea>
            </label>
            <label for="create-rating-form-grade" class="col-12 px-0">
                <span class="required">Quality</span>
                <span class="rating"></span><input type="hidden" name="grade" id="create-rating-form-grade" value="5">
            </label>
            <label for="create-rating-form-review-notes" class="invisible">
                <input name="review_dont_fill" type="text" value="" id="create-rating-form-review-notes">
            </label>
            <div class="col-12 px-0">
                <button class="btn btn-highlight form--add-review__-button" type="submit">Submit Review</button>
            </div>
        </div>
    </form>
</div>