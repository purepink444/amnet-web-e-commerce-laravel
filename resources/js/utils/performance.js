/**
 * Performance Monitoring Utilities
 * Web Vitals and custom performance metrics
 */

class PerformanceMonitor {
    constructor() {
        this.metrics = new Map();
        this.observers = [];
        this.init();
    }

    /**
     * Initialize performance monitoring
     */
    init() {
        // Monitor Core Web Vitals
        this.monitorWebVitals();

        // Monitor custom metrics
        this.monitorCustomMetrics();

        // Monitor resource loading
        this.monitorResourceLoading();

        // Monitor navigation timing
        this.monitorNavigationTiming();
    }

    /**
     * Monitor Core Web Vitals
     */
    monitorWebVitals() {
        // Largest Contentful Paint (LCP)
        if ('PerformanceObserver' in window) {
            try {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    this.recordMetric('LCP', lastEntry.startTime);
                    console.log(`📊 LCP: ${lastEntry.startTime.toFixed(2)}ms`);
                });
                lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
                this.observers.push(lcpObserver);
            } catch (error) {
                console.warn('LCP monitoring not supported:', error);
            }

            // First Input Delay (FID)
            try {
                const fidObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach((entry) => {
                        this.recordMetric('FID', entry.processingStart - entry.startTime);
                        console.log(`📊 FID: ${(entry.processingStart - entry.startTime).toFixed(2)}ms`);
                    });
                });
                fidObserver.observe({ entryTypes: ['first-input'] });
                this.observers.push(fidObserver);
            } catch (error) {
                console.warn('FID monitoring not supported:', error);
            }

            // Cumulative Layout Shift (CLS)
            try {
                let clsValue = 0;
                const clsObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach((entry) => {
                        if (!entry.hadRecentInput) {
                            clsValue += entry.value;
                        }
                    });
                    this.recordMetric('CLS', clsValue);
                    console.log(`📊 CLS: ${clsValue.toFixed(4)}`);
                });
                clsObserver.observe({ entryTypes: ['layout-shift'] });
                this.observers.push(clsObserver);
            } catch (error) {
                console.warn('CLS monitoring not supported:', error);
            }
        }
    }

    /**
     * Monitor custom performance metrics
     */
    monitorCustomMetrics() {
        // Time to Interactive approximation
        window.addEventListener('load', () => {
            setTimeout(() => {
                const tti = performance.now();
                this.recordMetric('TTI', tti);
                console.log(`📊 TTI: ${tti.toFixed(2)}ms`);
            }, 0);
        });

        // Monitor long tasks
        if ('PerformanceObserver' in window) {
            try {
                const longTaskObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach((entry) => {
                        if (entry.duration > 50) { // Tasks longer than 50ms
                            console.warn(`🚨 Long task detected: ${entry.duration.toFixed(2)}ms`);
                            this.recordMetric('LongTask', entry.duration);
                        }
                    });
                });
                longTaskObserver.observe({ entryTypes: ['longtask'] });
                this.observers.push(longTaskObserver);
            } catch (error) {
                console.warn('Long task monitoring not supported:', error);
            }
        }
    }

    /**
     * Monitor resource loading performance
     */
    monitorResourceLoading() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const resources = performance.getEntriesByType('resource');
                const slowResources = resources.filter(entry =>
                    entry.duration > 1000 // Resources taking more than 1 second
                );

                if (slowResources.length > 0) {
                    console.warn('🚨 Slow resource loading detected:');
                    slowResources.forEach(entry => {
                        console.warn(`   ${entry.name}: ${entry.duration.toFixed(2)}ms`);
                    });
                }

                // Record resource metrics
                const totalResources = resources.length;
                const totalSize = resources.reduce((sum, entry) => sum + (entry.transferSize || 0), 0);

                this.recordMetric('TotalResources', totalResources);
                this.recordMetric('TotalTransferSize', totalSize);

                console.log(`📊 Resources: ${totalResources} files, ${(totalSize / 1024 / 1024).toFixed(2)}MB transferred`);
            }, 100);
        });
    }

    /**
     * Monitor navigation timing
     */
    monitorNavigationTiming() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const navigation = performance.getEntriesByType('navigation')[0];
                if (navigation) {
                    const timing = {
                        'DNS Lookup': navigation.domainLookupEnd - navigation.domainLookupStart,
                        'TCP Connect': navigation.connectEnd - navigation.connectStart,
                        'Server Response': navigation.responseEnd - navigation.requestStart,
                        'Page Load': navigation.loadEventEnd - navigation.navigationStart,
                        'DOM Processing': navigation.domContentLoadedEventEnd - navigation.responseEnd
                    };

                    console.log('📊 Navigation Timing:');
                    Object.entries(timing).forEach(([metric, value]) => {
                        if (value > 0) {
                            console.log(`   ${metric}: ${value.toFixed(2)}ms`);
                            this.recordMetric(`Navigation_${metric.replace(' ', '')}`, value);
                        }
                    });
                }
            }, 0);
        });
    }

    /**
     * Record a performance metric
     */
    recordMetric(name, value, unit = 'ms') {
        const timestamp = Date.now();
        const metric = {
            name,
            value,
            unit,
            timestamp
        };

        // Store in memory
        if (!this.metrics.has(name)) {
            this.metrics.set(name, []);
        }
        this.metrics.get(name).push(metric);

        // Send to analytics if available
        this.sendToAnalytics(metric);

        // Keep only last 100 measurements per metric
        const metrics = this.metrics.get(name);
        if (metrics.length > 100) {
            metrics.shift();
        }
    }

    /**
     * Send metric to analytics service
     */
    sendToAnalytics(metric) {
        // Send to Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', 'performance_metric', {
                event_category: 'performance',
                event_label: metric.name,
                value: Math.round(metric.value),
                custom_map: {
                    metric_unit: metric.unit
                }
            });
        }

        // Send to custom analytics endpoint
        if (window.performanceEndpoint) {
            fetch(window.performanceEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(metric)
            }).catch(error => {
                console.warn('Failed to send performance metric:', error);
            });
        }
    }

    /**
     * Get performance metrics summary
     */
    getMetricsSummary() {
        const summary = {};

        for (const [name, metrics] of this.metrics) {
            const values = metrics.map(m => m.value);
            summary[name] = {
                count: values.length,
                average: values.reduce((a, b) => a + b, 0) / values.length,
                min: Math.min(...values),
                max: Math.max(...values),
                latest: values[values.length - 1]
            };
        }

        return summary;
    }

    /**
     * Measure execution time of a function
     */
    measureExecutionTime(name, fn) {
        const start = performance.now();
        const result = fn();
        const end = performance.now();

        this.recordMetric(`${name}_ExecutionTime`, end - start);
        console.log(`⏱️ ${name} executed in ${(end - start).toFixed(2)}ms`);

        return result;
    }

    /**
     * Monitor component render time
     */
    monitorComponentRender(componentName, renderFn) {
        return this.measureExecutionTime(`${componentName}_Render`, renderFn);
    }

    /**
     * Monitor API call performance
     */
    monitorApiCall(url, method = 'GET') {
        const startTime = performance.now();

        return {
            end: () => {
                const duration = performance.now() - startTime;
                this.recordMetric('API_Call_Duration', duration);
                console.log(`🌐 API ${method} ${url}: ${duration.toFixed(2)}ms`);

                // Alert on slow API calls
                if (duration > 2000) {
                    console.warn(`🚨 Slow API call detected: ${method} ${url} took ${duration.toFixed(2)}ms`);
                }
            }
        };
    }

    /**
     * Clean up observers
     */
    destroy() {
        this.observers.forEach(observer => {
            observer.disconnect();
        });
        this.observers = [];
        this.metrics.clear();
    }
}

// Create singleton instance
const performanceMonitor = new PerformanceMonitor();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceMonitor;
}

// Make available globally
window.PerformanceMonitor = PerformanceMonitor;
window.performanceMonitor = performanceMonitor;

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🚀 Performance monitoring initialized');
    });
} else {
    console.log('🚀 Performance monitoring initialized');
}