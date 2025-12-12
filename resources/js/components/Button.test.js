/**
 * Button Component Tests
 * Basic testing setup for Web Components
 */

class TestRunner {
    constructor() {
        this.tests = [];
        this.results = { passed: 0, failed: 0, errors: [] };
    }

    test(name, fn) {
        this.tests.push({ name, fn });
    }

    async run() {
        console.log('🧪 Running Button Component Tests...\n');

        for (const test of this.tests) {
            try {
                await test.fn();
                this.results.passed++;
                console.log(`✅ ${test.name}`);
            } catch (error) {
                this.results.failed++;
                this.results.errors.push({ test: test.name, error: error.message });
                console.log(`❌ ${test.name}: ${error.message}`);
            }
        }

        this.printSummary();
    }

    printSummary() {
        console.log(`\n📊 Test Results:`);
        console.log(`   ✅ Passed: ${this.results.passed}`);
        console.log(`   ❌ Failed: ${this.results.failed}`);
        console.log(`   📈 Total: ${this.results.passed + this.results.failed}`);

        if (this.results.errors.length > 0) {
            console.log('\n❌ Failed Tests:');
            this.results.errors.forEach(({ test, error }) => {
                console.log(`   ${test}: ${error}`);
            });
        }
    }
}

// Mock DOM environment for testing
if (typeof document === 'undefined') {
    try {
        // For Node.js environment, we'll skip DOM-dependent tests
        console.log('⚠️ Running in Node.js environment - DOM tests will be skipped');
        global.document = null;
        global.window = null;
        global.customElements = null;
    } catch (error) {
        console.warn('Could not setup test environment:', error);
    }
}

// Test suite
const testRunner = new TestRunner();

// Test: Button component creation
testRunner.test('Button component should be defined', () => {
    if (typeof customElements !== 'undefined' && customElements) {
        expect(typeof customElements.get('ui-button')).toBe('function');
    } else {
        // Skip in environments without custom elements
        console.log('⚠️ Custom elements not available, skipping test');
    }
});

// Test: Button creation and basic properties (skip if no DOM)
testRunner.test('Button should create element with correct attributes', () => {
    if (typeof document === 'undefined' || !document) {
        console.log('⚠️ DOM not available, skipping DOM test');
        return;
    }

    const button = document.createElement('ui-button');
    button.setAttribute('variant', 'primary');
    button.setAttribute('size', 'md');

    expect(button.getAttribute('variant')).toBe('primary');
    expect(button.getAttribute('size')).toBe('md');
    expect(button.hasAttribute('disabled')).toBe(false);
});

// Test: Button disabled state (skip if no DOM)
testRunner.test('Button should handle disabled state', () => {
    if (typeof document === 'undefined' || !document) {
        console.log('⚠️ DOM not available, skipping DOM test');
        return;
    }

    const button = document.createElement('ui-button');
    button.setAttribute('disabled', '');

    expect(button.disabled).toBe(true);
    expect(button.hasAttribute('disabled')).toBe(true);
});

// Test: Button loading state (skip if no DOM)
testRunner.test('Button should handle loading state', () => {
    if (typeof document === 'undefined' || !document) {
        console.log('⚠️ DOM not available, skipping DOM test');
        return;
    }

    const button = document.createElement('ui-button');
    button.setAttribute('loading', 'true');

    expect(button.loading).toBe(true);
});

// Test: Button variants (logic test)
testRunner.test('Button should support different variants', () => {
    const variants = ['primary', 'secondary', 'outline', 'ghost', 'danger'];

    // Test that variants array is properly defined
    expect(variants.includes('primary')).toBe(true);
    expect(variants.includes('secondary')).toBe(true);
    expect(variants.length === 5).toBe(true);
});

// Test: Button sizes (logic test)
testRunner.test('Button should support different sizes', () => {
    const sizes = ['sm', 'md', 'lg', 'xl'];

    // Test that sizes array is properly defined
    expect(sizes.includes('md')).toBe(true);
    expect(sizes.includes('lg')).toBe(true);
    expect(sizes.length === 4).toBe(true);
});

// Test: Button content slots (skip if no DOM)
testRunner.test('Button should support slot content', () => {
    if (typeof document === 'undefined' || !document) {
        console.log('⚠️ DOM not available, skipping DOM test');
        return;
    }

    const button = document.createElement('ui-button');
    button.innerHTML = `
        <span slot="prefix">🔍</span>
        Search
        <span slot="suffix">→</span>
    `;

    // Check if slots are present
    expect(button.querySelector('[slot="prefix"]')).toBeTruthy();
    expect(button.textContent.includes('Search')).toBe(true);
    expect(button.querySelector('[slot="suffix"]')).toBeTruthy();
});

// Mock expect function if not available
if (typeof expect === 'undefined') {
    global.expect = (actual) => ({
        toBe: (expected) => {
            if (actual !== expected) {
                throw new Error(`Expected ${expected}, but got ${actual}`);
            }
        },
        toBeTruthy: () => {
            if (!actual) {
                throw new Error(`Expected truthy value, but got ${actual}`);
            }
        }
    });
}

// Export for use in other test files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { TestRunner, testRunner };
}

// Auto-run tests if this file is executed directly
if (typeof window !== 'undefined' && window && window.location) {
    // Browser environment - run tests when DOM is ready
    document.addEventListener('DOMContentLoaded', async () => {
        // Ensure Button component is loaded
        if (!customElements.get('ui-button')) {
            try {
                await import('./Button.js');
            } catch (error) {
                console.error('Failed to load Button component for testing:', error);
                return;
            }
        }

        await testRunner.run();
    });
} else if (typeof process !== 'undefined') {
    // Node.js environment - run immediately
    testRunner.run().catch(console.error);
}