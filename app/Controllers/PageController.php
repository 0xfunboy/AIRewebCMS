<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Cms\ContentRepository;
use App\Services\Security\Csrf;
use App\Support\Flash;

final class PageController extends Controller
{
    private ContentRepository $content;

    public function __construct()
    {
        $this->content = new ContentRepository();
    }

    public function home(): void
    {
        $settings = $this->content->getSettings();
        $products = $this->content->getProducts();
        $agents = $this->content->getAgents();
        $partners = $this->content->getPartners();

        $this->view('public/home', compact('settings', 'products', 'agents', 'partners'));
    }

    public function products(): void
    {
        $products = $this->content->getProducts();
        $this->view('public/products', compact('products'));
    }

    public function product(string $slug): void
    {
        $product = $this->content->getProductBySlug($slug);

        if (!$product) {
            http_response_code(404);
            $this->view('public/not-found');
            return;
        }

        $this->view('public/product', compact('product'));
    }

    public function blog(): void
    {
        $posts = $this->content->getBlogPosts();
        $this->view('public/blog', compact('posts'));
    }

    public function blogPost(string $slug): void
    {
        $post = $this->content->getBlogPostBySlug($slug);

        if (!$post) {
            http_response_code(404);
            $this->view('public/not-found');
            return;
        }

        $this->view('public/blog-post', compact('post'));
    }

    public function agents(): void
    {
        $agents = $this->content->getAgents();
        $this->view('public/agents', compact('agents'));
    }

    public function team(): void
    {
        $team = $this->content->getTeamMembers();
        $this->view('public/team', compact('team'));
    }

    public function partners(): void
    {
        $partners = $this->content->getPartners();
        $this->view('public/partners', compact('partners'));
    }

    public function clients(): void
    {
        $caseStudies = $this->content->getCaseStudies();
        $this->view('public/clients', compact('caseStudies'));
    }

    public function commands(): void
    {
        $commands = $this->content->getCommands();
        $this->view('public/commands', compact('commands'));
    }

    public function socialProof(): void
    {
        $items = $this->content->getSocialProofItems();
        $this->view('public/social-proof', compact('items'));
    }

    public function roadmap(): void
    {
        $phases = $this->content->getRoadmapPhases();
        $tracks = $this->content->getAlwaysOnTracks();
        $settings = $this->content->getSettings();

        $this->view('public/roadmap', [
            'phases' => $phases,
            'tracks' => $tracks,
            'vision' => $settings['roadmap_vision'] ?? '',
        ]);
    }

    public function tokenomics(): void
    {
        $this->view('public/tokenomics');
    }

    public function transparency(): void
    {
        $settings = $this->content->getSettings();
        $this->view('public/transparency', compact('settings'));
    }

    public function apiPlugins(): void
    {
        $this->view('public/api-plugins');
    }

    public function press(): void
    {
        $assets = $this->content->getPressAssets();
        $this->view('public/press', compact('assets'));
    }

    public function legal(): void
    {
        $settings = $this->content->getSettings();
        $this->view('public/legal', compact('settings'));
    }

    public function faq(): void
    {
        $faqs = $this->content->getFaqItems();
        $this->view('public/faq', compact('faqs'));
    }

    public function contact(): void
    {
        $settings = $this->content->getSettings();
        $csrfToken = Csrf::token();
        $success = Flash::pull('contact_success');
        $error = Flash::pull('contact_error');

        $this->view('public/contact', compact('settings', 'csrfToken', 'success', 'error'));
    }
}
