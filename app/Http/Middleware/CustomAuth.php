<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class CustomAuth
{
    private $cacheDir;

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiToken = $request->header('Authorization');
        $url = $request->fullUrl();
        $method = $request->method();
       
        if (!$apiToken) {
            return response('Unauthenticated', 401);
        }
        
        $apiTokenInfo = $this->getInfoFromToken($apiToken);
        
        $username = $apiTokenInfo[1];
        $passwordDigest = $apiTokenInfo[2];
        $nounce = $apiTokenInfo[3];
        $created = $apiTokenInfo[4];

        $user = User::where('email', $username)->first();
        
        $validRoute = $this->validateRoute($user, $method, $url);
        $validDigest = $this->validateDigest($passwordDigest, $nounce, $created, $user->password);

        if ($validDigest && $validRoute && $user instanceof User) {
            return $next($request);
        } else {
            return response('Unauthenticated', 401);
        }
    }

    public function getInfoFromToken($apiToken)
    {
        preg_match('/UsernameToken Username="(.*?)", PasswordDigest="(.*?)", Nonce="(.*?)", Created="(.*?)"/', $apiToken, $matches);
        return $matches;
    }

    public function validateRoute(User $user, string $method, string $url) {        

        // Only the user can fetch their own posts (published/drafts/to be published)
        preg_match('/[\s\S]+\/posts\/([\d]+)/', $url, $getPostsMatches);
        if ($getPostsMatches && $getPostsMatches[0]) {
            return $user->id === intval($getPostsMatches[0]);
        }

        preg_match('/[\s\S]+\/post\/([\d]+)/', $url, $createPostMatches);
        if ($createPostMatches && $createPostMatches[0]) {
            return $user->id === intval($createPostMatches[0]);
        }


        return true;
    }

    protected function validateDigest($digest, $nonce, $created, $password)
    {
        $now = time() * 1000;

        // Expire timestamp after 5 minutes
        if ($now - intval($created) > 300000) {
            return false;
        }
        // Validate that the nonce is *not* used in the last 5 minutes
        // if it has, this could be a replay attack
        if (file_exists($this->cacheDir.'/'.$nonce) && file_get_contents($this->cacheDir.'/'.$nonce) + 300 > time()) {
            return false;
        }
        // If cache directory does not exist we create it
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
        file_put_contents($this->cacheDir.'/'.$nonce, time());

        // Validate Secret
        $expected = base64_encode(sha1(base64_decode($nonce).intval($created).$password, true));
        return Hash_equals($expected, $digest);
    }
}
