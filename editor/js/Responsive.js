// Responsive.js - Handles breakpoint logic
export const Breakpoints = {
    desktop: { name: 'Desktop', width: '100%' },
    tablet: { name: 'Tablet', width: '768px' },
    mobile: { name: 'Mobile', width: '375px' }
};

export const getResponsiveStyle = (nodeStyles, activeBreakpoint) => {
    // 1. Start with Desktop/Base styles
    let style = { ...nodeStyles.desktop };

    // 2. Override with Tablet if active and defined
    if (activeBreakpoint === 'tablet' && nodeStyles.tablet) {
        style = { ...style, ...nodeStyles.tablet };
    }

    // 3. Override with Mobile if active and defined
    if (activeBreakpoint === 'mobile' && nodeStyles.mobile) {
        style = { ...style, ...nodeStyles.mobile };
    }

    return style;
};
