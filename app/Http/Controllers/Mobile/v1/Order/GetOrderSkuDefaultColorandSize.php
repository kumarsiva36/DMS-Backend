<?php

namespace App\Http\Controllers\Mobile\v1\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Color;
use App\Models\Size;

class GetOrderSkuDefaultColorandSize extends Controller
{
    /**
     * Handle the incoming request.
     * Get the default Colors and Sizes
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereConditions = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id]
        ];
        $getOrderPage = [];
        $getOrderPage['color'] = $this->getDefaultColor($whereConditions);
        $getOrderPage['size'] = $this->getDefaultSize($whereConditions);
        return response()->json(["status_code" => 200, "status" => "Success", "data" => $getOrderPage]);
    }



      /* View  Default color Details */
      public function getDefaultColor($whereConditions)
      {
        $getColors = Color::where($whereConditions)->orWhere('is_default', '=', '0')->get();
          return $getColors;
      }

        /* View  Default Size Details */
        public function getDefaultSize($whereConditions)
        {
          $getSizes = Size::where($whereConditions)->orWhere('is_default', '=', '0')->get();
            return $getSizes;
        }
}


