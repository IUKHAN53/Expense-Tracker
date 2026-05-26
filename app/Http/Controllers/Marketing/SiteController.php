<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Support\RegionalPricing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteController extends Controller
{
    public function home(Request $request)
    {
        return $this->respondWithPricing($request, view('marketing.home', [
            'pricing' => RegionalPricing::detect($request),
        ]));
    }

    public function pricing(Request $request)
    {
        return $this->respondWithPricing($request, view('marketing.pricing', [
            'pricing' => RegionalPricing::detect($request),
            'regions' => RegionalPricing::all(),
        ]));
    }

    public function privacy()
    {
        return view('marketing.privacy');
    }

    public function terms()
    {
        return view('marketing.terms');
    }

    public function sitemap()
    {
        $today = now()->toDateString();
        $base = rtrim(url('/'), '/');

        $urls = [
            ['loc' => $base.'/',         'priority' => '1.0', 'changefreq' => 'monthly'],
            ['loc' => $base.'/pricing',  'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => $base.'/privacy',  'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => $base.'/terms',    'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($urls as $u) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($u['loc'], ENT_XML1).'</loc>'.PHP_EOL;
            $xml .= '    <lastmod>'.$today.'</lastmod>'.PHP_EOL;
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'.PHP_EOL;
            $xml .= '    <priority>'.$u['priority'].'</priority>'.PHP_EOL;
            $xml .= '  </url>'.PHP_EOL;
        }
        $xml .= '</urlset>'.PHP_EOL;

        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * If the request had an explicit ?cc=XX, persist it as a year-long cookie
     * so the chosen region sticks across pages without further query params.
     */
    private function respondWithPricing(Request $request, $view)
    {
        $cc = strtoupper((string) $request->query('cc', ''));

        if ($cc && RegionalPricing::isKnown($cc)) {
            return response($view)->cookie(
                RegionalPricing::COOKIE,
                $cc,
                60 * 24 * 365, // a year, in minutes
            );
        }

        return $view;
    }
}
