<?php
/**
 * Documentation View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('HID Commerce Documentation', 'hid-simple-commerce'); ?></h1>

    <div class="hid-settings-tabs">
        <nav>
            <a href="?page=hid-commerce-documentation&tab=dashboard" class="<?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>"><?php _e('Dashboard', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=products" class="<?php echo $active_tab == 'products' ? 'active' : ''; ?>"><?php _e('Products', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=categories" class="<?php echo $active_tab == 'categories' ? 'active' : ''; ?>"><?php _e('Categories', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=orders" class="<?php echo $active_tab == 'orders' ? 'active' : ''; ?>"><?php _e('Orders', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=customers" class="<?php echo $active_tab == 'customers' ? 'active' : ''; ?>"><?php _e('Customers', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=communications" class="<?php echo $active_tab == 'communications' ? 'active' : ''; ?>"><?php _e('Communications', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=settings" class="<?php echo $active_tab == 'settings' ? 'active' : ''; ?>"><?php _e('Settings', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=inquiries" class="<?php echo $active_tab == 'inquiries' ? 'active' : ''; ?>"><?php _e('Contact Inquiries', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-documentation&tab=requests" class="<?php echo $active_tab == 'requests' ? 'active' : ''; ?>"><?php _e('Jewelry Requests', 'hid-simple-commerce'); ?></a>
        </nav>

        <div class="hid-documentation-content" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 1000px;">
            
            <?php if ($active_tab == 'dashboard'): ?>
                <h2><?php _e('Dashboard Overview', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('The central hub for your store\'s performance and quick actions.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('Key Metrics & Cards', 'hid-simple-commerce'); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>Total Revenue:</strong> Displays the formatted sum of all completed orders.</li>
                    <li><strong>Total Orders:</strong> Count of all orders regardless of status.</li>
                    <li><strong>Low Stock Items:</strong> <span style="color: #f0b429;">Yellow warning count</span> showing combined low stock products and variants.</li>
                    <li><strong>Pending Orders:</strong> <span style="color: #3a5a8b;">Blue count</span> of orders that need your immediate attention.</li>
                </ul>

                <h3><?php _e('Sections', 'hid-simple-commerce'); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>Low Stock Alert:</strong> Appears only when items are below threshold. Lists specific product names and remaining quantity.</li>
                    <li><strong>Recent Orders:</strong> Table showing the last 5 orders with Status, Total, and Date. Click the Order Number to view details.</li>
                    <li><strong>Best Sellers:</strong> A ranked list of your top-performing products by quantity sold.</li>
                    <li><strong>Order Status Breakdown:</strong> A quick summary table showing how many orders are in each status (Pending, Processing, Completed, Cancelled).</li>
                </ul>

            <?php elseif ($active_tab == 'products'): ?>
                <h2><?php _e('Product Management', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Manage your inventory, pricing, and product details.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('How To: Add New Product', 'hid-simple-commerce'); ?></h3>
                <ol style="margin-left: 20px;">
                    <li>Click <strong>Add New</strong> next to the page title.</li>
                    <li><strong>Basic Info:</strong> Enter the <em>Product Name</em>, <em>Description</em>, and <em>SKU</em>.</li>
                    <li><strong>Categorization:</strong> Select a <em>Category</em> from the dropdown.</li>
                    <li><strong>Images:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li><strong>Primary Image:</strong> Paste an image URL or click "Upload Image" to select from media library.</li>
                            <li><strong>Gallery:</strong> Click "Add Gallery Images" to add multiple angles. You can drag and drop them to reorder.</li>
                        </ul>
                    </li>
                    <li><strong>Product Data:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li><strong>Simple Product:</strong> Leave "This product has variants" unchecked. Enter <em>Price</em>, <em>Stock Quantity</em>, and <em>Low Stock Threshold</em>.</li>
                            <li><strong>Variable Product:</strong> Check "This product has variants".
                                <br>1. Add <strong>Attributes</strong> (e.g., Size, Material).
                                <br>2. Click "Add Variant" to create rows.
                                <br>3. For each variant, enter Name, Price, Stock, and SKU.
                            </li>
                        </ul>
                    </li>
                    <li><strong>SEO:</strong> Fill in <em>Meta Title</em> and <em>Description</em> for search engines.</li>
                    <li><strong>Status:</strong> Set to "Published" to make live, or "Draft" to hide. Toggle "Featured" to display on the homepage.</li>
                    <li>Click <strong>Add Product</strong> (or Update Product) to save.</li>
                </ol>

                <h3><?php _e('How To: Export Products', 'hid-simple-commerce'); ?></h3>
                <p>Click the <strong>Export</strong> button at the top of the page to download a CSV of your entire catalog.</p>

            <?php elseif ($active_tab == 'categories'): ?>
                <h2><?php _e('Category Management', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Organize products into a hierarchy.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('How To: Create a Category', 'hid-simple-commerce'); ?></h3>
                <p>The "Add New Category" form is always visible on the left side of the page.</p>
                <ol style="margin-left: 20px;">
                    <li><strong>Name:</strong> Enter the display name (Required).</li>
                    <li><strong>Parent Category:</strong> Select "None (Top Level)" for main categories, or choose an existing category to make this a sub-category.</li>
                    <li><strong>Image:</strong> Upload an image to represent this category on the frontend grid.</li>
                    <li><strong>Display Order:</strong> Enter a number (0, 1, 2...) to control the sort order in menus. Lower numbers appear first.</li>
                    <li>Click <strong>Add Category</strong>.</li>
                </ol>

                <h3><?php _e('How To: Edit/Delete', 'hid-simple-commerce'); ?></h3>
                <p>In the "Existing Categories" table on the right:</p>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>Edit:</strong> Click the "Edit" link below a category name. The form on the left will populate with its data. Click "Update Category" to save changes.</li>
                    <li><strong>Delete:</strong> Click "Delete" to remove. <em>Note: This does not delete the products inside, but they will become uncategorized.</em></li>
                </ul>

            <?php elseif ($active_tab == 'orders'): ?>
                <h2><?php _e('Order Management', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Track and fulfill customer orders.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('Order Workflow', 'hid-simple-commerce'); ?></h3>
                <ol style="margin-left: 20px;">
                    <li><strong>New Order:</strong> Appears in the list with status <span class="hid-order-status pending">Pending</span> or <span class="hid-order-status processing">Processing</span>.</li>
                    <li><strong>View Details:</strong> Click the <strong>View</strong> button or the Order Number to open the detail screen.</li>
                    <li><strong>Check Payment:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li><strong>Online (Paystack/Stripe/PayPal):</strong> Status will be "Paid" automatically.</li>
                            <li><strong>Bank Transfer:</strong> Check "Payment Proof" section (if user uploaded receipt). Verify funds in your bank. Click the <strong>Confirm Payment Received</strong> button to mark as Paid.</li>
                        </ul>
                    </li>
                    <li><strong>Fulfillment:</strong> Pack items found in the "Order Items" table. Note the Shipping Address and Courier.</li>
                    <li><strong>Update Status:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li>Change dropdown to <strong>Completed</strong> when shipped/delivered.</li>
                            <li>Change dropdown to <strong>Cancelled</strong> if the order is void.</li>
                            <li>Click "Update" (this triggers email notifications).</li>
                        </ul>
                    </li>
                </ol>

                <h3><?php _e('Filters & Export', 'hid-simple-commerce'); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li>Use the <strong>Status Dropdown</strong> above the table to show only Pending or Completed orders.</li>
                    <li>Click <strong>Export</strong> to download a CSV report of orders for accounting.</li>
                </ul>

            <?php elseif ($active_tab == 'customers'): ?>
                <h2><?php _e('Customer Database', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('View registered customers and newsletter subscribers.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('Customer Types', 'hid-simple-commerce'); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><span style="background:#d4edda; color:#155724; padding:2px 6px; border-radius:4px; font-size:11px;">CUSTOMER</span> Users who have placed an order.</li>
                    <li><span style="background:#d1ecf1; color:#0c5460; padding:2px 6px; border-radius:4px; font-size:11px;">SUBSCRIBER</span> Users who signed up for the newsletter but haven't purchased yet.</li>
                </ul>

                <h3><?php _e('Actions', 'hid-simple-commerce'); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>Search:</strong> Use the search box to find a customer by name or email.</li>
                    <li><strong>Delete:</strong> Remove a customer record permanently.</li>
                    <li><strong>Stats:</strong> View the "Total", "Customers", and "Subscribers" cards at the top for a quick count.</li>
                </ul>

            <?php elseif ($active_tab == 'communications'): ?>
                <h2><?php _e('Communications', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Built-in email marketing tool.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('How To: Send a Bulk Email', 'hid-simple-commerce'); ?></h3>
                <ol style="margin-left: 20px;">
                    <li><strong>Target Audience:</strong> Select from the dropdown:
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li><em>All:</em> Everyone in database.</li>
                            <li><em>Customers Only:</em> People who bought products.</li>
                            <li><em>Subscribers Only:</em> Newsletter leads.</li>
                        </ul>
                    </li>
                    <li><strong>Subject:</strong> Enter the email subject line.</li>
                    <li><strong>Message:</strong> Use the rich text editor to compose your email. You can bold text, add links, lists, etc.</li>
                    <li><strong>Preview:</strong> Click "Preview Email" to see the branded template with your logo and colors.</li>
                    <li><strong>Send:</strong> Click "Send Email". A confirmation alert will appear. <strong>Do not close the browser</strong> while the batch process runs (indicated by loading state).</li>
                </ol>

            <?php elseif ($active_tab == 'settings'): ?>
                <h2><?php _e('System Settings', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Global configuration for your store.', 'hid-simple-commerce'); ?></p>
                <hr>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4><?php _e('General', 'hid-simple-commerce'); ?></h4>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <li><strong>Admin Email:</strong> Where system notifications are sent.</li>
                            <li><strong>Logo:</strong> Toggle "Use text logo" or upload a custom image.</li>
                            <li><strong>Branding:</strong> Set Primary, Secondary, Accent, and Background colors.</li>
                            <li><strong>Typography:</strong> Define Header and Body font families (e.g., Dancing Script, Montserrat).</li>
                        </ul>
                    </div>
                    <div>
                        <h4><?php _e('Payments', 'hid-simple-commerce'); ?></h4>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <li><strong>Enable Gateways:</strong> Checkboxes for Paystack, Stripe, PayPal, Bank Transfer.</li>
                            <li><strong>Credentials:</strong> Enter Public/Secret keys for each enabled gateway.</li>
                            <li><strong>Bank Info:</strong> Fill in Bank Name, Account Number, etc. for transfer instructions.</li>
                        </ul>
                    </div>
                    <div>
                        <h4><?php _e('Inventory & Currency', 'hid-simple-commerce'); ?></h4>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <li><strong>Currency:</strong> Set Code (NGN, USD), Symbol, and position.</li>
                            <li><strong>Stock:</strong> Define global "Low Stock Threshold" (default 10).</li>
                            <li><strong>Out of Stock:</strong> Choose to "Hide product" or "Show as unavailable".</li>
                        </ul>
                    </div>
                    <div>
                        <h4><?php _e('Social & Email', 'hid-simple-commerce'); ?></h4>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <li><strong>Links:</strong> Enter URLs for Instagram, Facebook, etc. to appear in footer.</li>
                            <li><strong>Alerts:</strong> Set comma-separated emails for low stock notifications.</li>
                        </ul>
                    </div>
                </div>

            <?php elseif ($active_tab == 'inquiries'): ?>
                <h2><?php _e('Contact Inquiries', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Messages from the "Contact Us" page form.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('Reading Inquiries', 'hid-simple-commerce'); ?></h3>
                <p>The list shows <strong>Name</strong>, <strong>Email</strong>, <strong>Subject</strong> (General, Order, Return...), and <strong>Date</strong>.</p>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>Status:</strong> New messages are marked <strong>Unread</strong> (Bold). Opening them automatically marks them as <strong>Read</strong>.</li>
                </ul>

                <h3><?php _e('Managing an Inquiry', 'hid-simple-commerce'); ?></h3>
                <ol style="margin-left: 20px;">
                    <li>Click on the <strong>Subject</strong> or "View" to open the message.</li>
                    <li>Read the full message content.</li>
                    <li><strong>Internal Actions:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px;">
                            <li><strong>Admin Notes:</strong> Write private notes about the conversation in the text area.</li>
                            <li><strong>Status:</strong> Manually toggle between Read/Unread if needed.</li>
                            <li>Click <strong>Save Changes</strong> to update the record.</li>
                        </ul>
                    </li>
                    <li>To reply, click the customer's email link to open your default email client.</li>
                </ol>

            <?php elseif ($active_tab == 'requests'): ?>
                <h2><?php _e('Jewelry Requests', 'hid-simple-commerce'); ?></h2>
                <p class="description"><?php _e('Custom bespoke piece requests from clients.', 'hid-simple-commerce'); ?></p>
                <hr>

                <h3><?php _e('Request Pipeline', 'hid-simple-commerce'); ?></h3>
                <p>Track requests through these stages using the <strong>Status</strong> dropdown:</p>
                <ul style="list-style-type: none; margin-left: 20px;">
                    <li>⏳ <strong>Pending:</strong> New request, hasn't been looked at.</li>
                    <li>🔍 <strong>Reviewing:</strong> You are assessing the feasibility/budget.</li>
                    <li>📞 <strong>Contacted:</strong> You have reached out to the client.</li>
                    <li>✅ <strong>Completed:</strong> The project is finished or deal closed.</li>
                    <li>❌ <strong>Cancelled:</strong> Request valid but not proceeding.</li>
                </ul>

                <h3><?php _e('Reviewing Details', 'hid-simple-commerce'); ?></h3>
                <ol style="margin-left: 20px;">
                    <li>Click to view a Request.</li>
                    <li>Check the <strong>Jewelry Type</strong> (Ring, Necklace, etc.) and <strong>Budget Range</strong>.</li>
                    <li>Read the <strong>Description</strong> carefully for design notes.</li>
                    <li>Use <strong>Admin Notes</strong> to log call summaries or price quotes given to the client.</li>
                </ol>

            <?php endif; ?>
        </div>
    </div>
</div>
