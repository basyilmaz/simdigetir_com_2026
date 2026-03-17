<style id="sg-design-tokens">
    :root {
        /* Shared brand tokens */
        --sg-brand-primary: #7c3aed;
        --sg-brand-primary-light: #a78bfa;
        --sg-brand-primary-dark: #5b21b6;
        --sg-brand-secondary: #6366f1;
        --sg-brand-accent: #22d3ee;
        --sg-brand-accent-2: #f472b6;
        --sg-brand-gradient: linear-gradient(135deg, #f97316, #ef4444);
        --sg-action-gradient: linear-gradient(135deg, #f97316, #ea580c);
        --sg-gradient-primary: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
        --sg-gradient-accent: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);
        --sg-gradient-purple: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #22d3ee 100%);
        --sg-gradient-glow: radial-gradient(ellipse at center, rgba(124, 58, 237, 0.15) 0%, transparent 70%);
        --sg-accent-warm-glow: rgba(249, 115, 22, 0.24);
        --sg-accent-cool-glow: rgba(14, 165, 233, 0.18);

        /* Shared surface tokens */
        --sg-surface-page-light: linear-gradient(180deg, #fff7ed, #f3ede5);
        --sg-surface-page-dark: linear-gradient(180deg, #0b1220, #0f172a);
        --sg-surface-landing-dark: #0c0118;
        --sg-surface-landing-darker: #06000d;
        --sg-surface-landing-light: #f8f9fc;
        --sg-surface-landing-light-alt: #eef0f5;
        --sg-ink-light: #201b17;
        --sg-ink-dark: #e5e7eb;
        --sg-ink-dark-soft: #e2e8f0;
        --sg-ink-contrast: #ffffff;
        --sg-muted-light: #6b6258;
        --sg-muted-dark: #cbd5e1;

        --sg-border-light: rgba(63, 42, 20, 0.12);
        --sg-border-light-soft: rgba(63, 42, 20, 0.08);
        --sg-border-light-dashed: rgba(63, 42, 20, 0.16);
        --sg-border-dark: rgba(148, 163, 184, 0.16);
        --sg-border-dark-strong: rgba(148, 163, 184, 0.22);
        --sg-border-glass-dark: rgba(255, 255, 255, 0.08);
        --sg-border-glass-light: rgba(0, 0, 0, 0.1);
        --sg-border-glow-dark: rgba(124, 58, 237, 0.3);
        --sg-border-glow-light: rgba(124, 58, 237, 0.2);

        --sg-card-light: rgba(255, 255, 255, 0.82);
        --sg-card-light-soft: rgba(255, 255, 255, 0.68);
        --sg-card-light-muted: rgba(255, 255, 255, 0.8);
        --sg-card-light-strong: rgba(255, 255, 255, 0.94);
        --sg-card-light-overlay: rgba(255, 255, 255, 0.03);
        --sg-card-dark: rgba(15, 23, 42, 0.74);
        --sg-card-dark-soft: rgba(15, 23, 42, 0.55);
        --sg-card-dark-muted: rgba(15, 23, 42, 0.42);
        --sg-card-dark-strong: rgba(15, 23, 42, 0.85);
        --sg-card-dark-compact: rgba(15, 23, 42, 0.5);
        --sg-card-brand-soft: rgba(124, 58, 237, 0.08);
        --sg-card-brand-soft-light: rgba(124, 58, 237, 0.06);

        --sg-shadow-light: 0 18px 60px rgba(63, 42, 20, 0.12);
        --sg-shadow-dark: 0 18px 60px rgba(2, 6, 23, 0.45);

        /* Shared state tokens */
        --sg-accent-warm-bg-soft: rgba(249, 115, 22, 0.08);
        --sg-accent-warm-bg: rgba(249, 115, 22, 0.12);
        --sg-accent-warm-bg-strong: rgba(249, 115, 22, 0.2);
        --sg-accent-warm-border: rgba(249, 115, 22, 0.16);
        --sg-accent-warm-border-strong: rgba(251, 146, 60, 0.45);
        --sg-accent-warm-surface-strong: rgba(124, 45, 18, 0.38);
        --sg-accent-warm-text: #9a3412;
        --sg-accent-warm-text-strong: #fdba74;
        --sg-focus-border: rgba(234, 88, 12, 0.4);
        --sg-focus-ring: rgba(249, 115, 22, 0.12);
        --sg-success-bg: rgba(22, 163, 74, 0.12);
        --sg-success-bg-strong: rgba(34, 197, 94, 0.16);
        --sg-success-border-strong: rgba(74, 222, 128, 0.32);
        --sg-success-surface-strong: rgba(20, 83, 45, 0.3);
        --sg-success-text: #166534;
        --sg-success-text-strong: #bbf7d0;
        --sg-error-bg: rgba(180, 35, 24, 0.12);
        --sg-error-bg-strong: rgba(239, 68, 68, 0.16);
        --sg-error-text: #9d1c12;
        --sg-error-text-strong: #fecaca;
        --sg-info-bg: rgba(33, 150, 243, 0.12);
        --sg-info-bg-strong: rgba(59, 130, 246, 0.16);
        --sg-info-text: #164a8b;
        --sg-info-text-strong: #bfdbfe;
        --sg-warning-bg-strong: rgba(245, 158, 11, 0.16);
        --sg-warning-text-strong: #fde68a;
        --sg-teal-bg-strong: rgba(20, 184, 166, 0.2);
        --sg-teal-text: #99f6e4;
        --sg-link-warm: #c2410c;

        /* Shared glass tokens */
        --sg-glass-surface-strong: rgba(255, 255, 255, 0.07);
        --sg-glass-surface-soft: rgba(255, 255, 255, 0.02);
        --sg-glass-stroke: rgba(255, 255, 255, 0.14);
        --sg-glass-stroke-hover: rgba(124, 58, 237, 0.45);
        --sg-glass-highlight: rgba(34, 211, 238, 0.24);
        --sg-glass-blur-size: 18px;
        --sg-glass-shadow-soft: 0 16px 40px rgba(6, 0, 13, 0.32);
        --sg-glass-shadow-hover: 0 24px 52px rgba(124, 58, 237, 0.22);
        --sg-glass-shadow-inset: inset 0 1px 0 rgba(255, 255, 255, 0.1);
        --sg-glass-form-focus: 0 0 0 3px rgba(124, 58, 237, 0.2);
    }

    [data-theme="light"] {
        --sg-glass-surface-strong: rgba(255, 255, 255, 0.86);
        --sg-glass-surface-soft: rgba(255, 255, 255, 0.62);
        --sg-glass-stroke: rgba(99, 102, 241, 0.16);
        --sg-glass-stroke-hover: rgba(124, 58, 237, 0.34);
        --sg-glass-highlight: rgba(34, 211, 238, 0.18);
        --sg-glass-shadow-soft: 0 12px 30px rgba(15, 23, 42, 0.08);
        --sg-glass-shadow-hover: 0 20px 44px rgba(99, 102, 241, 0.16);
        --sg-glass-shadow-inset: inset 0 1px 0 rgba(255, 255, 255, 0.75);
        --sg-glass-form-focus: 0 0 0 3px rgba(99, 102, 241, 0.14);
    }
</style>
