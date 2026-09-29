<?php

namespace App\Twig;

use App\Repository\SocialNetworkRepository;
use App\Repository\PageContentRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private SocialNetworkRepository $socialNetworkRepo;
    private PageContentRepository $pageContentRepository;
    private string $publicDir;

    public function __construct(
        RequestStack $requestStack,
        SocialNetworkRepository $socialNetworkRepo,
        PageContentRepository $pageContentRepository,
        #[Autowire('%kernel.project_dir%')] string $projectDir,
    ) {
        $this->publicDir = $projectDir . '/public';
        $this->requestStack = $requestStack;
        $this->socialNetworkRepo = $socialNetworkRepo;
        $this->pageContentRepository = $pageContentRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_item_class', [$this, 'navItemClass'], ['is_safe' => ['html']]),
            new TwigFunction('get_social_networks', [$this, 'getSocialNetworks']),
            new TwigFunction('get_public_pages', [$this, 'getPublicPages']),
            new TwigFunction('public_file_exists', [$this, 'publicFileExists']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('role_label', [$this, 'roleLabel']),
        ];
    }

    public function navItemClass(string $routePrefix): string
    {
        $currentRouteName = $this->requestStack->getCurrentRequest()?->attributes->get('_route') ?? '';
        if (strpos($currentRouteName, $routePrefix) !== false) {
            return 'class="nav-link nav-link--active" aria-current="page"';
        }
        return 'class="nav-link" ';
    }

    /**
     * Converts a Symfony role string to a human-readable label.
     *
     * Usage: {{ 'ROLE_ADMIN'|role_label }}  → 'Administrador'
     */
    public function roleLabel(string $role): string
    {
        return match ($role) {
            'ROLE_ADMIN' => 'Administrador',
            'ROLE_USER'  => 'Usuário',
            default      => ucfirst(strtolower(str_replace(['ROLE_', '_'], ['', ' '], $role))),
        };
    }

    public function getSocialNetworks(): array
    {
        return $this->socialNetworkRepo->findBy(['isActive' => true], ['position' => 'ASC']);
    }

    public function getPublicPages(): array
    {
        return $this->pageContentRepository->findBy(['isActive' => true]);
    }

    public function publicFileExists(string $path): bool
    {
        $path = ltrim($path, '/');

        return !str_contains($path, '..') && is_file($this->publicDir . '/' . $path);
    }
}
