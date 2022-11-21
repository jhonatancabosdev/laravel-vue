<?php

namespace App\Repositories;

use App\Review;
use App\Product;
use App\Exceptions\ProductReviewCreateErrorException;

class ReviewRepository
{
    /**
     * ProductReviewRepository constructor.
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->model = $review;
    }


    // Need CRUD methods
    public function getLatestReviews( $number=10 ) {
        return $this->model
            ->where('title', '!=', '')
            ->where('grade', '>=', '4')
            ->where('description', '!=', '')
            ->orderByRaw('SUBSTRING(date,6) desc,SUBSTRING(date,3,5) desc,SUBSTRING(date,0,2) desc,id desc')
            ->limit($number)
            ->get();
    }

    /**
     * Find the average review rating
     *
     * @return Float
     * 
     */
    public function getAverageGrade() {
        return $this->model
            ->avg('grade');
    }

    /**
     * Create the product
     *
     * @param array $data
     *
     * @return Product
     * @throws CreateErrorException
     */
    public function createReview(array $data) : Review
    {
        try {
            return $this->model::create($data);
        } catch (QueryException $e) {
            throw new ReviewCreateErrorException($e);
        }
    }
}
