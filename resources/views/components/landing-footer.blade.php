<!-- Global Landing Footer Component -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="/" class="brand-logo">
                    <img src="{{ asset('logo/infrahub-logo-dark.png') }}" alt="InfraHub" style="height: 44px; object-fit: contain;">
                </a>
                <p>
                    InfraHub connects people, processes, and project data across the entire lifecycle—from planning to handover and beyond. ISO 19650 compliant CDE platform.
                </p>
            </div>

            <div class="footer-col">
                <h4>Products</h4>
                <a href="/products#operations">Project Schedule & Tasks</a>
                <a href="/products#site-resources">Inventory & Stores</a>
                <a href="/products#site-resources">SHEQ & Safety Management</a>
                <a href="/products#commercial-cost">BOQ & Cost Control</a>
                <a href="/products#collaboration">ISO 19650 Document CDE</a>
                <a href="/products#commercial-cost">Tenders & Bids</a>
            </div>

            <div class="footer-col">
                <h4>Solutions</h4>
                <a href="/solutions#contractors">For Main Contractors</a>
                <a href="/solutions#subcontractors">For Subcontractors</a>
                <a href="/solutions#field-safety">For Safety & Field Crews</a>
                <a href="/solutions#cost-managers">For Quantity Surveyors</a>
                <a href="/get-started">Company Onboarding</a>
                <a href="/schedule-call">Schedule a Live Demo</a>
            </div>

            <div class="footer-col">
                <h4>Resources & Legal</h4>
                <a href="/docs">User Manual & Guides</a>
                <a href="/academy">InfraHub Academy</a>
                <a href="/pricing">Pricing & Plans</a>
                <a href="/about">About InfraHub</a>
                <a href="/health" target="_blank">System Status (Health)</a>
                <a href="/app/login">Login to Platform</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} InfraHub Platform Ltd. All rights reserved. Enterprise Construction Infrastructure Management.</p>
            <div style="display: flex; gap: 20px;">
                <a href="/docs" style="color: #64748b; text-decoration: none;">Documentation</a>
                <a href="/privacy" style="color: #64748b; text-decoration: none;">Privacy Policy</a>
                <a href="/terms" style="color: #64748b; text-decoration: none;">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
