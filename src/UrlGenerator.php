<?php


namespace AmrSoliman\AssetsCdn;


use Illuminate\Routing\RouteCollection;
use Illuminate\Http\Request;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{


    protected $cdnURL;
    protected $commitID;

    /**
     * Create a new URL Generator instance.
     *
     * @param  \Illuminate\Routing\RouteCollection $routes
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(RouteCollection $routes, Request $request)
    {
        parent::__construct($routes, $request);

        if (\Cache::has('assets-cdn::commitID')) {
            $this->commitID = \Cache::get('assets-cdn::commitID');
        } else {
            $this->commitID = exec('git rev-parse HEAD');
            \Cache::forever('assets-cdn::commitID', $this->commitID);
        }

        $this->cdnURL = config('assets-cdn.cdn-url');

    }

    /**
     * Generate a URL to an application asset.
     *
     * @param  string $path
     * @param  bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }
        $relativePath = config('assets-cdn.relative_path');

        $url = ($relativePath) ? '//' : $this->getScheme($secure);

        $url .= trim($this->cdnURL, '/');

        $url .= '/' . trim($path, '/');


        $url .= '?' . md5($this->commitID);

        return $url;
    }

}