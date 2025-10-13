<?php
declare(strict_types=1);

return [
    'settings' => [
        ['setting_key' => 'site_name', 'setting_value' => 'AIRewardrop'],
        ['setting_key' => 'site_tagline', 'setting_value' => 'Autonomous Agent Infrastructure for Crypto'],
        ['setting_key' => 'contact_email', 'setting_value' => 'dev@airewardrop.xyz'],
        ['setting_key' => 'business_telegram', 'setting_value' => 'https://t.me/funboynft'],
        ['setting_key' => 'hero_title_home', 'setting_value' => 'Autonomous Agent Infrastructure for Crypto'],
        ['setting_key' => 'hero_subtitle_home', 'setting_value' => 'AIRewardrop designs, ships, and operates always-on agents across multiple chains - live charts, on-chain analytics, and tokenized dApps. We’re the builders behind AIR3 and the Agent Swarm.'],
        ['setting_key' => 'hero_image_home', 'setting_value' => '/media/svg/hero/hero-default.svg'],
        ['setting_key' => 'hero_badge_home', 'setting_value' => '#4 Most Credible Agent by Ethos Network (Q4 2025).'],
        ['setting_key' => 'site_logo', 'setting_value' => '/media/svg/logo/site-logo.svg'],
        ['setting_key' => 'favicon_path', 'setting_value' => '/favicon.ico'],
        ['setting_key' => 'og_image', 'setting_value' => '/media/svg/hero/hero-default.svg'],
        ['setting_key' => 'seo_meta_title', 'setting_value' => 'AIRewardrop | Autonomous Agent Infrastructure for Crypto'],
        ['setting_key' => 'seo_meta_description', 'setting_value' => 'AIRewardrop builds, deploys, and maintains autonomous agents that deliver real-time market intelligence, on-chain automation, and community engagement.'],
        ['setting_key' => 'seo_social_title', 'setting_value' => 'AIRewardrop – Autonomous Agent Infrastructure'],
        ['setting_key' => 'seo_social_description', 'setting_value' => 'Discover AIRewardrop’s multi-chain agent stack powering analytics, execution, and community automation.'],
        ['setting_key' => 'seo_twitter_description', 'setting_value' => 'AIRewardrop | Autonomous agents delivering analytics, execution, and community engagement.'],
        ['setting_key' => 'seo_telegram_description', 'setting_value' => 'The AIR3 ecosystem delivers agent automation, analytics, and community utilities across chains.'],
        ['setting_key' => 'seo_discord_description', 'setting_value' => 'AIRewardrop supplies always-on agents, analytics, and automation for crypto teams.'],
        ['setting_key' => 'seo_share_image', 'setting_value' => '/media/svg/hero/hero-default.svg'],
        ['setting_key' => 'roadmap_vision', 'setting_value' => <<<'TEXT'
AIRewardrop builds, maintains, and deploys autonomous agents for the crypto and DeFi world - agents that analyze markets in real time, talk, visualize data, act on-chain, and generate verifiable economic value. The goal is not to make "a single bot," but to create a framework of multi-chain, multi-purpose agents, each capable of operating autonomously or for partner teams, all connected by the $AIR3 token and a shared revenue model.
TEXT
],
    ],
    'products' => [
        [
            'name' => 'AIR3 (Solana Agent)',
            'slug' => 'air3-solana-agent',
            'description' => '24/7 MetaHuman that answers in chat, renders charts, runs on-chain + sentiment analysis, and posts autonomously.',
            'icon_key' => 'chip',
            'external_link' => 'https://air3solana.xyz',
            'hero_title' => 'AIR3 (Solana Agent)',
            'hero_subtitle' => 'Our flagship 24/7 MetaHuman agent, blending live data, on-chain analytics, and autonomous engagement directly into your social feeds.',
            'cta_text' => 'Visit AIR3 Portal',
            'cta_link' => 'https://air3solana.xyz',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">The Always-On Analyst</h3>
<p class="text-muted mt-4">AIR3 is more than just a bot; it's a persistent, AI-driven entity that lives on the Solana blockchain and interacts with the world through social media. It can be triggered by anyone to provide instant, data-rich insights without ever leaving your conversation.</p>
<p class="text-muted mt-4">From price charts to in-depth analysis, AIR3 delivers what you need, when you need it. It performs sentiment checks, launches detailed reports, and publishes scheduled content to keep your community engaged.</p>
HTML
        ],
        [
            'name' => 'AIRtrak',
            'slug' => 'airtrak',
            'description' => 'A web app for transparent trading with Active/Pending Positions, PnL, filters, and shareable closed trades.',
            'icon_key' => 'cube',
            'external_link' => 'https://airtrack.airewardrop.xyz',
            'hero_title' => 'AIRtrak',
            'hero_subtitle' => 'Transparent trading intelligence with verifiable positions, analytics, and public performance feeds.',
            'cta_text' => 'Launch AIRtrak',
            'cta_link' => 'https://airtrack.airewardrop.xyz',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Verifiable Performance</h3>
<p class="text-muted mt-4">AIRtrak ingests raw on-chain transactions and reconciles them into a clean trading journal. Every entry links back to the original transaction hash, making it effortless to audit results.</p>
<ul class="space-y-2 mt-4">
    <li>• Understand unrealized vs. realized PnL at a glance.</li>
    <li>• Generate shareable reports for investors or community updates.</li>
    <li>• Filter by strategy, asset, or timeframe to surface actionable patterns.</li>
</ul>
HTML
        ],
        [
            'name' => 'Telegram/Discord Agents',
            'slug' => 'telegram-discord-agents',
            'description' => 'Token-gated access for communities to request charts, price, sentiment, and news with configurable rails.',
            'icon_key' => 'device-phone',
            'hero_title' => 'Telegram & Discord Agents',
            'hero_subtitle' => 'Deploy chat-native agents that respond instantly with price, charts, sentiment, and analytics inside your community.',
            'cta_text' => 'Request Deployment',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Community-Ready Automation</h3>
<p class="text-muted mt-4">Plug our command framework into your server and gate premium functionality behind staking thresholds or NFT ownership. Each action can be throttled, logged, and themed for your brand.</p>
<p class="text-muted mt-4">Agents deliver charts, news, and sentiment summaries within seconds, keeping communities informed without moderator overhead.</p>
HTML
        ],
        [
            'name' => 'Sniper & Bundle Bot',
            'slug' => 'sniper-bundle-bot',
            'description' => 'High-performance execution tools for early entries and efficient, gas-optimized order handling on-chain.',
            'icon_key' => 'paper-airplane',
            'hero_title' => 'Sniper & Bundle Bot',
            'hero_subtitle' => 'Execution-grade tooling for capturing early entries with intelligent routing and gas optimizations.',
            'cta_text' => 'Talk to Team',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Low-Latency Execution</h3>
<p class="text-muted mt-4">Bundles orders across DEXs, routes through private mempools, and enforces slippage + failover logic tuned for volatile launches.</p>
<p class="text-muted mt-4">Operators configure target tokens, acceptable liquidity pools, and risk profiles from an admin console—ideal for trading desks that need automation without sacrificing control.</p>
HTML
        ],
        [
            'name' => 'dApp Modules',
            'slug' => 'dapp-modules',
            'description' => 'Integratable staking vaults, pay-per-feature services, marketplace credits, and automated burn actions.',
            'icon_key' => 'beaker',
            'hero_title' => 'dApp Modules',
            'hero_subtitle' => 'Composable modules that plug into existing dApps: staking vaults, pay-per-feature credits, burn automations.',
            'cta_text' => 'Explore Modules',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Composable Building Blocks</h3>
<p class="text-muted mt-4">Choose from plug-and-play components—vaults, credit ledgers, affiliate tracking, scheduled burn mechanics—and blend them into your existing dApp with minimal engineering lift.</p>
<p class="text-muted mt-4">Each module ships with audit-ready smart contracts and an admin UI so your team can orchestrate campaigns without redeploying code.</p>
HTML
        ],
        [
            'name' => 'White-label Agents',
            'slug' => 'whitelabel-agents',
            'description' => 'Custom agents delivered for XRPL, Berachain, Cronos, and more. Your brand, our engine.',
            'icon_key' => 'sparkles',
            'hero_title' => 'White-label Agents',
            'hero_subtitle' => 'Deliver your own branded AI agent with our production infrastructure and on-chain integrations.',
            'cta_text' => 'Start a Project',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Custom Agents, Production Ready</h3>
<p class="text-muted mt-4">We map your tone, product surface, and desired outcomes into a deployable AI agent powered by our runtime. Delivery includes scene design for MetaHuman deployments, full command libraries, and a go-live checklist.</p>
<p class="text-muted mt-4">Support contracts keep the agent trained on new data sources and aligned with evolving compliance or brand requirements.</p>
HTML
        ],
        [
            'name' => 'Agent Studio & Marketplace',
            'slug' => 'agent-studio-marketplace',
            'description' => 'Build and sell agent templates/plugins. Power users can create and share in the revenue stream.',
            'icon_key' => 'storefront',
            'hero_title' => 'Agent Studio & Marketplace',
            'hero_subtitle' => 'Create, package, and sell agent templates and plugins to the entire AIRewardrop ecosystem.',
            'cta_text' => 'Join Waitlist',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Creator Economy for Agents</h3>
<p class="text-muted mt-4">Agent Studio offers a visual builder and SDK so power users can publish templates, monetise plugins, and share in revenue driven by downstream deployments.</p>
<p class="text-muted mt-4">Marketplace distribution includes versioning, licensing, and analytics. Contributors earn automatically when teams subscribe to their modules.</p>
HTML
        ],
        [
            'name' => 'Staking Vault',
            'slug' => 'staking-vault',
            'description' => 'Lock your tokens to gain access to premium features, earn rewards, and participate in governance.',
            'icon_key' => 'wallet',
            'hero_title' => 'Staking Vault',
            'hero_subtitle' => 'Stake AIR3 to unlock premium features, earn rewards, and participate in long-term governance.',
            'cta_text' => 'View Tiers',
            'cta_link' => '/contact',
            'content_html' => <<<'HTML'
<h3 class="text-2xl font-bold text-acc">Aligned Incentives</h3>
<p class="text-muted mt-4">The staking vault powers utility unlocks: access private agents, boost revenue share, and vote on roadmap priorities. Tiers can be tuned for partners vs. retail communities.</p>
<p class="text-muted mt-4">Rewards automatically distribute from trading and licensing revenue, with transparent reporting via AIRtrak.</p>
HTML
        ],
    ],
    'product_features' => [
        'air3-solana-agent' => [
            'Live Chart Rendering in Social Feeds',
            'On-Chain and Technical Analysis',
            'Real-time Social Sentiment Analysis',
            'Autonomous Social Posting & Engagement',
            'Multi-platform: X, Telegram, Discord',
        ],
    ],
    'agents' => [
        [
            'name' => 'AIR3',
            'chain' => 'Solana',
            'status' => 'Live',
            'summary' => 'Our flagship 24/7 MetaHuman agent providing live charts, on-chain analysis, and autonomous social engagement.',
            'site_url' => 'https://air3solana.xyz',
            'image_url' => 'https://picsum.photos/seed/air3/500/300',
            'badge' => 'Main Agent',
        ],
        [
            'name' => 'PolarAI',
            'chain' => 'Berachain',
            'status' => 'Live',
            'summary' => 'A specialized agent deployed on the Berachain network, providing insights and analytics for its ecosystem.',
            'site_url' => 'https://polarberai.xyz',
            'image_url' => 'https://picsum.photos/seed/polarai/500/300',
        ],
        [
            'name' => 'LAIR',
            'chain' => 'Cronos',
            'status' => 'Live',
            'summary' => 'The premiere agent for the Cronos chain, offering tools and data for traders and projects on Cronos.',
            'site_url' => 'https://www.laircronos.xyz',
            'image_url' => 'https://picsum.photos/seed/lair/500/300',
        ],
        [
            'name' => 'Avalanche Agent',
            'chain' => 'Avalanche',
            'status' => 'In Development',
            'summary' => 'Our upcoming expansion to the Avalanche ecosystem, bringing our powerful agent framework to a new network.',
            'site_url' => '#',
            'image_url' => 'https://picsum.photos/seed/avalanche/500/300',
        ],
    ],
    'team_members' => [
        [
            'name' => 'funboy',
            'role' => 'Business Development & Unreal Engine Dev',
            'bio' => 'Owns and operates IT/TLC companies and a private hardware farm. Driving the vision and partnerships for AIRewardrop.',
            'avatar_url' => 'https://picsum.photos/seed/funboy/200',
            'telegram_url' => 'https://t.me/funboynft',
            'x_url' => null,
        ],
        [
            'name' => 'TheBadCooper',
            'role' => 'Full-stack Developer',
            'bio' => 'Expert in Rust, Solidity, JS/TS, React, and C++. The core architect of our on-chain and off-chain infrastructure.',
            'avatar_url' => 'https://picsum.photos/seed/badcooper/200',
            'telegram_url' => null,
            'x_url' => null,
        ],
        [
            'name' => 'Math',
            'role' => 'Quantitative Analyst & TA',
            'bio' => 'Author of popular TradingView indicators and the mind behind the strategies powering our trading agents.',
            'avatar_url' => 'https://picsum.photos/seed/math/200',
            'telegram_url' => null,
            'x_url' => null,
        ],
    ],
    'partners' => [
        [
            'name' => 'SKEW',
            'logo_url' => 'https://picsum.photos/seed/skew/200/100',
            'url' => 'https://skew.net',
            'summary' => 'Active partnership for P2P lending + debit card; building concierge agent with per-user instances.',
            'status' => 'Active',
        ],
        [
            'name' => 'Meteora',
            'logo_url' => 'https://picsum.photos/seed/meteora/200/100',
            'url' => '#',
            'summary' => 'Utilizing Meteora\'s Airlock for token rewards and deep liquidity on Solana via their DLMM pools.',
            'status' => 'Active',
        ],
        [
            'name' => 'Custodiy',
            'logo_url' => 'https://picsum.photos/seed/custodiy/200/100',
            'url' => '#',
            'summary' => 'Official listing partner, ensuring secure and trusted access to our tokens.',
            'status' => 'Active',
        ],
        [
            'name' => 'CoinGecko',
            'logo_url' => 'https://picsum.photos/seed/coingecko/200/100',
            'url' => '#',
            'summary' => 'Listed on the world\'s largest independent crypto data aggregator.',
            'status' => 'Active',
        ],
        [
            'name' => 'Ethos Network',
            'logo_url' => 'https://picsum.photos/seed/ethos/200/100',
            'url' => '#',
            'summary' => 'Ranked #8 among the Top 10 most credible agents in the ecosystem. (Q3 2024)',
            'status' => 'Active',
        ],
        [
            'name' => 'Wolfstreet (Cronos)',
            'logo_url' => 'https://picsum.photos/seed/wolfstreet/200/100',
            'url' => '#',
            'summary' => 'Graduated from our program with ongoing integrations on the Cronos chain.',
            'status' => 'Active',
        ],
        [
            'name' => 'BONK / BEST',
            'logo_url' => 'https://picsum.photos/seed/bonk/200/100',
            'url' => '#',
            'summary' => 'Exploring potential integrations and partnerships within the BONK and BEST ecosystems.',
            'status' => 'In Discussion',
        ],
        [
            'name' => 'Pilot3AI',
            'logo_url' => 'https://picsum.photos/seed/pilot3ai/200/100',
            'url' => '#',
            'summary' => 'Discussing the development of a bespoke avatar agent for their innovative dApp.',
            'status' => 'In Discussion',
        ],
    ],
    'roadmap_phases' => [
        [
            'phase_label' => 'Phase 1 - Foundation Layer',
            'phase_key' => 'phase-1-foundation-layer',
            'timeline' => 'Q1–Q2 2025',
            'goal' => 'Consolidate the core technology and validate the model on Solana.',
            'items' => [
                [
                    'title' => 'AIR3 Core Runtime',
                    'description' => 'Proprietary fork of ElizaOS, expanded into a production-grade orchestrator with action routing, data parsing, and real-time AI command handling across multiple channels (X, Telegram, Discord, Twitch, YouTube).',
                ],
                [
                    'title' => 'Local LLM Stack',
                    'description' => 'Integration of DeepSeek-R1 and Janus-Pro 7B for local reasoning and vision, reducing OPEX and dependency on third-party APIs.',
                ],
                [
                    'title' => 'MetaHuman Live Interface',
                    'description' => 'Unreal Engine 5.5 setup for a 3D avatar that speaks, displays charts, and streams 24/7 with auto lipsync, animated scenes, and safe responses.',
                ],
                [
                    'title' => '$AIR3 Token (Solana)',
                    'description' => 'Deflationary utility token powering all services through lock, stake, pay-per-feature, and buyback & burn mechanics. LP burned, dev wallet <2%, active Meteora Airlock program rewarding top holders.',
                ],
                [
                    'title' => 'Public Deployment of AIR3 Agent',
                    'description' => 'First live AI agent streaming continuously on X, Twitch, and YouTube: providing analysis, charts, news, and sentiment in real time.',
                ],
            ],
        ],
        [
            'phase_label' => 'Phase 2 - Productization & Expansion',
            'phase_key' => 'phase-2-productization-expansion',
            'timeline' => 'Q3 2025',
            'goal' => 'Transition AIR3 from showcase to a full multi-service product.',
            'items' => [
                [
                    'title' => 'AIRtrak (Web App)',
                    'description' => 'The first dApp module. Displays open and closed positions, Unrealized PnL, equity curve, and trading stats (win rate, total trades, PnL, etc.). Brings complete transparency to the agent’s trading logic and performance.',
                ],
                [
                    'title' => 'Private dApp Portal (Beta)',
                    'description' => 'User dashboard with wallet connect, profile, staking vaults, service menu, and credit system for premium features.',
                ],
                [
                    'title' => 'Community Agent Deployment',
                    'description' => 'Framework for launching custom community agents on Discord and Telegram by locking $AIR3. → Example: Lock 100,000 AIR3 for 180 days = 6 months of access.',
                ],
                [
                    'title' => 'Voice & Live Monetization',
                    'description' => '$AIR3 payments for live voice spots, shoutouts, automated reports, and scheduled posts.',
                ],
                [
                    'title' => 'B2B White-Label Program',
                    'description' => 'Licensing framework for partner teams: delivery of customized text or MetaHuman agents with shared revenue and ongoing support from AIRewardrop.',
                ],
            ],
        ],
        [
            'phase_label' => 'Phase 3 - Agent Swarm & Ecosystem Growth',
            'phase_key' => 'phase-3-agent-swarm-ecosystem-growth',
            'timeline' => 'Q4 2025',
            'goal' => 'Build a distributed ecosystem of interoperable agents.',
            'items' => [
                [
                    'title' => 'Agent Swarm Framework',
                    'description' => 'Each agent (AIR3, PolarAI, LAIR, and future ones) shares the same infrastructure, exchanging analytical data, signals, and insights cross-chain.',
                ],
                [
                    'title' => 'New Chain Expansions',
                    'description' => "Deployment of chain-specific agents:\n\nPolarAI (Berachain) – operational and growing.\n\nLAIR (Cronos) – active with dApp under development and OG partnerships.\n\nXRPL Agent – custom white-label delivery completed.\n\nNext in line: Base / Avalanche agents.",
                ],
                [
                    'title' => 'Plugin & Template Marketplace (Agent Studio)',
                    'description' => 'Marketplace for power users and partners to sell templates, plugins, and modules built for their own agents.',
                ],
                [
                    'title' => 'Staking Vault Launch',
                    'description' => "Launch of staking and trading-profit redistribution:\n\nTiered staking benefits.\n\nAutomatic buybacks from trading revenue.\n\nProportional distribution to holders and contributors.",
                ],
                [
                    'title' => 'API & Partner Toolkit',
                    'description' => 'Public endpoints to integrate agent data (price feeds, sentiment, reports) into third-party products and DeFi dashboards.',
                ],
            ],
        ],
        [
            'phase_label' => 'Phase 4 - Consolidation & Commercialization',
            'phase_key' => 'phase-4-consolidation-commercialization',
            'timeline' => 'Q1–Q2 2026',
            'goal' => 'Scale infrastructure and create sustainable value for $AIR3.',
            'items' => [
                [
                    'title' => 'Full AIR3 dApp Rollout',
                    'description' => 'All modules unified: AIRtrak + Vault + Portal + Marketplace + Companion Instances. Users can spend $AIR3 to access any service or deploy their own agent.',
                ],
                [
                    'title' => 'Companion Agents (One-to-One)',
                    'description' => 'Private instances with dedicated memory, personality, and custom portfolio/risk management functions for premium users.',
                ],
                [
                    'title' => 'Autotrading Engine (Testnet)',
                    'description' => 'Implementation of an execution engine based on AIR3’s trade signals, automating real buybacks from realized profits. Governance-controlled, transparent via AIRtrak.',
                ],
                [
                    'title' => 'Corporate & B2B Expansion',
                    'description' => "Growing partnerships:\n\nSkew (active) – peer-to-peer lending + debit card AI concierge.\n\nPilot3AI (under discussion) – AI avatar for their dApp.\n\nBest/BONK (under discussion) – meme-driven engagement dApp.\nAll running under the AIRewardrop white-label framework.",
                ],
                [
                    'title' => 'Public API Monetization',
                    'description' => 'Usage-based plans for third-party integrations (data, analytics, reports).',
                ],
            ],
        ],
        [
            'phase_label' => 'Phase 5 - Governance & Decentralization',
            'phase_key' => 'phase-5-governance-decentralization',
            'timeline' => 'Late 2026 → 2027',
            'goal' => 'Gradual handover of governance and value distribution to community and partners.',
            'items' => [
                [
                    'title' => 'Governance Layer',
                    'description' => 'On-chain voting for treasury allocation, roadmap priorities, and design choices.',
                ],
                [
                    'title' => 'Automated Treasury Operations',
                    'description' => 'Buyback & burn logic tied to actual dApp and licensing revenue flow.',
                ],
                [
                    'title' => 'Agent DAO Model',
                    'description' => 'DAO framework for partner agents to share revenue, contribute modules, and participate in the AIRewardrop ecosystem.',
                ],
                [
                    'title' => 'Open SDK',
                    'description' => 'Developer kit for external teams to build and extend their own AI agents using the AIRewardrop framework.',
                ],
            ],
        ],
    ],
    'always_on_tracks' => [
        'Ongoing R&D on local LLMs and inference optimization.',
        'Continuous MetaHuman and Unreal Engine upgrades.',
        'Partner and chain integrations.',
        'Legal setup and compliance (Panama registration).',
        'Marketing & social expansion (X, Twitch, YouTube, Telegram, Discord).',
        'Continuous support for partner agents (PolarAI, LAIR, XRPL, and upcoming ones).',
    ],
    'commands' => [
        ['command' => '@AIRewardrop chart $TICKER', 'description' => 'Generates a price chart for the specified token symbol.'],
        ['command' => '@AIRewardrop chart <contract_address>', 'description' => "Generates a price chart using the token's contract address."],
        ['command' => '@AIRewardrop analysis $TICKER', 'description' => 'Provides a detailed on-chain and technical analysis report.'],
        ['command' => '@AIRewardrop price $TICKER', 'description' => 'Fetches the current price of the specified token.'],
        ['command' => '@AIRewardrop sentiment $TICKER', 'description' => 'Analyzes recent social media sentiment for the token.'],
        ['command' => '@AIRewardrop news $TICKER', 'description' => 'Retrieves the latest news articles related to the token or project.'],
    ],
    'case_studies' => [
        [
            'client' => 'XRPL Project',
            'chain' => 'XRPL',
            'title' => 'Launch Support & Analytics Agent',
            'summary' => 'Deployed a custom-branded agent to provide on-ledger analytics, price tracking, and community engagement during a major project launch on the XRP Ledger. The agent successfully serviced thousands of requests, improving community sentiment and information flow.',
            'image_url' => 'https://picsum.photos/seed/xrpl-case/600/400',
        ],
        [
            'client' => 'Berachain DeFi Protocol',
            'chain' => 'Berachain',
            'title' => 'Ecosystem Intelligence Bot',
            'summary' => 'Built and delivered a specialized agent for a leading Berachain protocol. The agent monitors protocol-specific metrics, provides liquidity pool analysis, and integrates with their governance forum, becoming a vital tool for their user base.',
            'image_url' => 'https://picsum.photos/seed/bera-case/600/400',
        ],
        [
            'client' => 'LAIR Cronos',
            'chain' => 'Cronos',
            'title' => 'Community Growth & Utility',
            'summary' => 'Provided launch support for LAIR on Cronos, integrating our agent framework as a core utility. The agent became a key driver for community engagement and demonstrated the power of our white-label solution in a new ecosystem.',
            'image_url' => 'https://picsum.photos/seed/lair-case/600/400',
        ],
        [
            'client' => 'Skew',
            'chain' => 'Multi-Chain',
            'title' => 'AI Concierge for P2P Lending & Debit Card',
            'summary' => "Building a bespoke concierge agent with per-user instances to support Skew's innovative P2P lending platform and debit card services. The agent provides personalized support and on-demand financial data.",
            'image_url' => 'https://picsum.photos/seed/skew-case/600/400',
        ],
    ],
    'press_assets' => [
        ['asset_type' => 'Logo', 'label' => 'Logo Pack (SVG, PNG)', 'file_path' => '#'],
        ['asset_type' => 'Brand Guide', 'label' => 'Brand Guidelines PDF', 'file_path' => '#'],
        ['asset_type' => 'One-Pager', 'label' => 'Company One-Pager PDF', 'file_path' => '#'],
    ],
    'social_proof_items' => [
        [
            'content_type' => 'Tweet',
            'author_name' => 'Crypto Influencer',
            'author_handle' => '@InfluencerX',
            'author_avatar_url' => 'https://picsum.photos/seed/influencer1/100',
            'content' => 'The @AIRewardrop agent is incredibly fast. Pulled a chart for $SOL in seconds right in my feed. This is the future of on-chain data.',
            'link' => 'https://x.com/AIRewardrop',
        ],
        [
            'content_type' => 'Tweet',
            'author_name' => 'DeFi Trader',
            'author_handle' => '@TraderJane',
            'author_avatar_url' => 'https://picsum.photos/seed/traderjane/100',
            'content' => 'Using AIRtrak to monitor my positions has been a game-changer for transparency. Finally, a tool that shows verifiable PnL. Highly recommend.',
            'link' => 'https://x.com/AIRewardrop',
        ],
        [
            'content_type' => 'Testimonial',
            'author_name' => 'Partner Project CEO',
            'author_handle' => 'Cronos Ecosystem',
            'author_avatar_url' => 'https://picsum.photos/seed/partnerceo/100',
            'content' => 'The AIRewardrop team delivered our white-label agent ahead of schedule. It has become an indispensable tool for our community on Cronos.',
            'link' => '/clients',
        ],
        [
            'content_type' => 'Tweet',
            'author_name' => 'NFT Collector',
            'author_handle' => '@NFTKing',
            'author_avatar_url' => 'https://picsum.photos/seed/nftking/100',
            'content' => 'Just asked the @AIRewardrop bot for sentiment analysis on a new mint. The data it provided was super helpful for my decision. Wow.',
            'link' => 'https://x.com/AIRewardrop',
        ],
        [
            'content_type' => 'Media',
            'author_name' => 'CryptoNews Weekly',
            'author_handle' => 'Media Outlet',
            'author_avatar_url' => 'https://picsum.photos/seed/cryptonews/100',
            'content' => '"AIRewardrop is quietly building some of the most practical AI-driven infrastructure in the Web3 space, with live products to prove it."',
            'link' => '#',
        ],
        [
            'content_type' => 'Testimonial',
            'author_name' => 'Community Manager',
            'author_handle' => 'Berachain Project',
            'author_avatar_url' => 'https://picsum.photos/seed/communitymgr/100',
            'content' => 'Our Discord is so much more active since we installed the AIRewardrop agent. Members love the instant access to charts and data.',
            'link' => '/clients',
        ],
    ],
    'faq_items' => [
        [
            'question' => 'What is AIRewardrop?',
            'answer' => 'AIRewardrop is a development organization specializing in autonomous AI agents for the Web3 space. We build the core infrastructure, dApp modules, and white-label agent solutions like AIR3, our flagship MetaHuman agent on Solana.',
        ],
        [
            'question' => 'What is the utility of the AIR3 token?',
            'answer' => 'The AIR3 token is the central utility token for our ecosystem. It is used to access premium features, deploy community and white-label agents, pay for marketplace services, and stake for rewards and governance. Revenue generated is used for a buyback and burn program.',
        ],
        [
            'question' => 'Can I get a custom agent for my project?',
            'answer' => 'Yes. We offer a comprehensive white-label service where we build and deploy a custom-branded agent tailored to your project\'s specific needs and blockchain ecosystem. Please get in touch with our team to discuss your requirements.',
        ],
        [
            'question' => 'How can my project partner with AIRewardrop?',
            'answer' => 'We are always open to collaborations with innovative projects. Partnerships can range from simple integrations with our data APIs to co-development of new agent capabilities. Please use the contact form or reach out to us on Telegram to start a discussion.',
        ],
    ],
    'blog_posts' => [
        [
            'slug' => 'agent-swarm-expansion',
            'title' => 'Agent Swarm Expansion: Announcing Our Move to Avalanche',
            'date' => 'October 28, 2024',
            'image_url' => 'https://picsum.photos/seed/blog-avax/600/400',
            'snippet' => 'We are thrilled to announce the next phase of our multi-chain expansion. The AIRewardrop agent framework is officially in development for the Avalanche ecosystem, bringing our powerful suite of tools to a new network.',
            'content' => "We are thrilled to announce the next phase of our multi-chain expansion. The AIRewardrop agent framework is officially in development for the Avalanche ecosystem, bringing our powerful suite of tools to a new network.\n\nThis move represents a significant step in our mission to create a truly interoperable network of autonomous agents. The high throughput and low fees of Avalanche make it an ideal environment for our on-chain analytics and execution modules.",
        ],
        [
            'slug' => 'airtrak-v2-launch',
            'title' => 'AIRtrak V2 is Here: Verifiable Performance, Redefined',
            'date' => 'October 15, 2024',
            'image_url' => 'https://picsum.photos/seed/blog-airtrak/600/400',
            'snippet' => 'Today marks a major milestone for our ecosystem. We\'re launching AIRtrak V2, a complete overhaul of our transparent trading dashboard. With a new UI, advanced analytics, and shareable performance cards, tracking on-chain activity has never been easier.',
            'content' => "Today marks a major milestone for our ecosystem. We're launching AIRtrak V2, a complete overhaul of our transparent trading dashboard. With a new UI, advanced analytics, and shareable performance cards, tracking on-chain activity has never been easier.\n\nThe new version allows users to generate verifiable, shareable reports of their trading performance, a game-changer for traders who value transparency and data-driven improvement.",
        ],
        [
            'slug' => 'the-power-of-local-llms',
            'title' => 'The Power of Local LLMs in Crypto Agents',
            'date' => 'September 30, 2024',
            'image_url' => 'https://picsum.photos/seed/blog-llm/600/400',
            'snippet' => 'A deep dive into our tech stack. Learn why we\'re integrating local Large Language Models like DeepSeek-R1 and Janus-Pro 7B to reduce latency, cut operational costs, and enhance the reasoning capabilities of our agents.',
            'content' => "A deep dive into our tech stack. Learn why we're integrating local Large Language Models like DeepSeek-R1 and Janus-Pro 7B to reduce latency, cut operational costs, and enhance the reasoning capabilities of our agents.\n\nBy moving away from a dependency on third-party APIs for core reasoning, we gain greater control over performance, security, and the unique 'personality' of each agent we deploy. This is a crucial step towards building truly autonomous and resilient on-chain entities.",
        ],
    ],
    'always_on_tracks_meta' => [
        'title' => 'Always On Track',
    ],
];
