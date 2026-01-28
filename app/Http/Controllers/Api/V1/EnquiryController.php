<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreEnquiryRequest;

class EnquiryController extends ApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreEnquiryRequest $request)
    {
        $request->validated();

        Enquiry::create($request->validated());

        return $this->successResponse('Enquiry created successfully');
    }
}
