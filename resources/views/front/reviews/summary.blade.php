<div class="d-flex flex-column">
    <div class="d-flex">
        @include('front.reviews.stars',
            ['rating' => $averageRating ]
            )
        <small class="px-2 py-1">{{ number_format($averageRating, 1) }} stars</small>
    </div>
    <p class="py-1">based upon {{ count($reviews) }} reviews</p>
</div>
