<?php

namespace TCG\Voyager\Http\Controllers;

use App\Order;
use App\Product;
use App\Category;
use League\Flysystem\Util;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Intervention\Image\Constraint;
use TCG\Voyager\Charts\SalesChart;
use TCG\Voyager\Charts\OrdersChart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Charts\ProductsChart;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class VoyagerController extends Controller
{
    public function index()
    {
        if (! request()->ajax()) {
            $salesChart = new SalesChart();
            $ordersChart = new OrdersChart();
            $productsChart = (new ProductsChart())
                ->setData()
                ->minimalist(true)
                ->displayLegend(true)
                ->height(290);

            return Voyager::view('voyager::index', [
                'salesProducts' => Product::orderBy('sales_count', 'desc')->where('sales_count', '>', 0)->take(10)->get(),
                'visitedProducts' => Product::orderBy('clicks_counter', 'desc')->where('clicks_counter', '>', 0)->take(10)->get(),
                'salesChart' => $salesChart->setData(),
                'ordersChart' => $ordersChart->setData(),
                'productsChart' => $productsChart
            ]);
        }

        $analytics = (Object) [
            'today_orders' => (Object) [
                'today' => Order::today()->confirmed()->count(),
                'yesterday' => Order::whereDate('created_at', now()->subDay())->confirmed()->count(),
            ],
            'today_sales' => (Object) [
                'today' => round(Order::today()->confirmed()->sum('subtotal'), 2),
                'yesterday' => round(Order::whereDate('created_at', now()->subDay())->confirmed()->sum('subtotal'), 2),
            ],
            'today_avg_sales' => (Object) [
                'today' => round(Order::today()->confirmed()->avg('subtotal'), 2),
                'yesterday' => round(Order::whereDate('created_at', now()->subDay())->confirmed()->avg('subtotal'), 2),
            ],
            'all_time_avg_sales' => (Object) [
                'today' => round(Order::confirmed()->avg('subtotal'), 2),
                'yesterday' => round(Order::confirmed()->avg('subtotal'), 2),
            ],
            'all_time' => (Object) [
                'today' => round(Order::confirmed()->sum('subtotal'), 2),
                'yesterday' => round(Order::confirmed()->sum('subtotal'), 2),
            ],
        ];

        $completed = Order::confirmed()->count();
        $abandoned = Order::notConfirmed()->count();

        $isCheckoutBug = $completed !== 0 && $completed <= $abandoned;

        return response()->json(
            compact('analytics', 'completed', 'abandoned', 'isCheckoutBug')
        );
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('voyager.login');
    }

    /**
     * @return void
     */
    public function clearCache()
    {
        try {
            try {
                Artisan::call('view:clear');
                Artisan::call('responsecache:clear');
            } catch (\Exception $e) {
            }

            try {
                Product::flushCache();
                Category::flushCache();
            } catch (\Exception $e) {
            }
        } catch (\Exception $e) {
        }

        return back()->with([
            'message'    => "Cache cleared successfully",
            'alert-type' => 'success',
        ]);
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $path = $slug.'/'.date('F').date('Y').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            if ($ext !== 'gif') {
                $image->orientate();
            }
            $image->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = __('voyager::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('voyager::media.error_uploading');
            }
        } else {
            $status = __('voyager::media.uploading_wrong_type');
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.helpers.setImageValue('".Voyager::image($fullFilename)."'); </script>";
    }

    public function assets(Request $request)
    {
        try {
            $path = dirname(__DIR__, 3).'/publishable/assets/'.Util::normalizeRelativePath(urldecode($request->path));
        } catch (\LogicException $e) {
            abort(404);
        }

        if (File::exists($path)) {
            $mime = '';

            if (Str::endsWith($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (Str::endsWith($path, '.css')) {
                $mime = 'text/css';
            } else {
                $mime = File::mimeType($path);
            }
            
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);

            if (config('app.env') !== 'local') {
                $response->setSharedMaxAge(31536000);
                $response->setMaxAge(31536000);
                $response->setExpires(new \DateTime('+1 year'));
            }

            return $response;
        }

        return response('', 404);
    }
}
