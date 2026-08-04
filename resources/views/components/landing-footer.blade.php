<!-- Global Landing Footer Component -->
<footer>
    <div class="container">
        <div class="footer-grid" style="grid-template-columns: 1.5fr 1fr 1fr 1.2fr;">
            <div class="footer-brand">
                <a href="/" class="brand-logo">
                    <img src="{{ asset('logo/infrahub-logo-dark.png') }}" alt="InfraHub" style="height: 44px; object-fit: contain;">
                </a>
                <span style="display: block; font-size: 11px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; color: #f97316; margin-top: 6px;">A JOADAH TECHNOLOGIES PLATFORM</span>
                <p style="margin-top: 10px;">
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
                <a href="/about">About & Global Offices</a>
                <a href="/schedule-call">Schedule a Live Demo</a>
            </div>

            <div class="footer-col">
                <h4>Global Offices</h4>
                <div style="font-size: 13px; line-height: 1.5; margin-bottom: 14px;">
                    <strong style="color: #f97316; display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Africa HQ — Entebbe</strong>
                    Plot 48 Church Road, Entebbe, Uganda
                </div>
                <div style="font-size: 13px; line-height: 1.5;">
                    <strong style="color: #38bdf8; display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Regional Office — USA</strong>
                    1007 N Orange Street, 4th Floor, Suite 1382, Wilmington, DE 19801
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} InfraHub Platform &middot; A Joadah Technologies Platform. All rights reserved.</p>
            <div style="display: flex; gap: 20px;">
                <a href="/docs" style="color: #64748b; text-decoration: none;">Documentation</a>
                <a href="/privacy" style="color: #64748b; text-decoration: none;">Privacy Policy</a>
                <a href="/terms" style="color: #64748b; text-decoration: none;">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
