<?php
/** @var array $settings */
$contactEmail = $settings['contact_email'] ?? 'legal@airewardrop.xyz';
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Legal Center',
            'subtitle' => 'Terms, privacy, and cookie policies for the AIRewardrop ecosystem.',
        ]); ?>
    </div>

    <section class="space-y-12 max-w-4xl mx-auto text-sm leading-relaxed text-muted">
        <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate>
            <h2 class="text-2xl font-bold text-acc mb-4">Terms of Service</h2>
            <p class="mb-4">By accessing AIRewardrop properties, including the public website, APIs, and authenticated dashboards, you agree to operate in good faith, respect intellectual property, and comply with applicable laws. Services may change or be discontinued at any time. Accounts proven to abuse rate limits, violate partner agreements, or attempt to gain unauthorized access may be suspended.</p>
            <p class="mb-0">Professional services delivered under bespoke statements of work are also governed by the applicable contract terms agreed with each partner.</p>
        </article>

        <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate data-animate-delay="120">
            <h2 class="text-2xl font-bold text-acc mb-4">Privacy Policy</h2>
            <p class="mb-4">We collect minimal personal data: contact details voluntarily submitted through forms, analytics to improve our products, and operational logs required to keep the platform secure. We do not sell user data. Partners engaging in white-label deployments may operate under additional privacy agreements that extend these protections.</p>
            <p class="mb-0">You may request access or deletion of stored personal data by reaching out to <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" class="text-cy hover:underline"><?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?></a>.</p>
        </article>

        <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate data-animate-delay="220">
            <h2 class="text-2xl font-bold text-acc mb-4">Cookie Policy</h2>
            <p class="mb-4">The public website uses strictly necessary cookies for session management and lightweight analytics. Optional analytics cookies can be disabled through your browser preferences. Third-party embeds (e.g., YouTube, social widgets) may set their own cookies; their policies apply in those cases.</p>
            <p class="mb-0">Continuing to browse the site indicates your consent to the cookie usage described above.</p>
        </article>
    </section>
</div>
