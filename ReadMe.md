# UbuntuBay: C2C E-Commerce Platform for the South African Informal Economy

UbuntuBay is a lightweight, mobile-responsive Consumer-to-Consumer (C2C) web application explicitly engineered to empower South African informal traders, street vendors, and township entrepreneurs. It bridges the gap between complex backend digital commerce and an intuitive, accessible user interface, allowing everyday users to safely list products, chat directly with local buyers, arrange localized shipping options, and update live inventory without requiring technical expertise.

---

## Project Architecture & Directory Structure

The project code is organized cleanly into modular folders to guarantee separation of concerns, easy database configuration, and frictionless deployment to free cloud-hosting environments.

UbuntuBay/
│
├── assets/
│ ├── css/
│ │ └── style.css # Custom UI styling, Bootstrap overrides
│ ├── js/
│ │ ├── script.js # Live-pricing calculation, form validation
│ │ └── chat.js # AJAX/Fetch API for real-time chat
│ └── images/ # Static assets, icons, user uploads
│
├── includes/
│ ├── header.php # Global navbar, mobile toggle-menus
│ ├── footer.php # Sticky footer with copyrights
│ └── database.php # MySQL credentials & connection
│
├── admin/ # Secure Admin Panel (RBAC Protected)
│ ├── index.php # Dashboard with analytics
│ ├── login.php # Admin authorization portal
│ ├── logout.php # Terminates admin session
│ ├── users.php # Moderate/suspend user accounts
│ ├── listings.php # Moderate active listings
│ └── transactions.php # Master transaction ledger
│
├── diagrams/ # Deliverable 2 Design Diagrams
│ ├── use_case.png # System Interaction Diagram
│ ├── context_diagram.png # DFD Level 0 Context Map
│ ├── dfd_level1.png # DFD Level 1 Process Map
│ ├── eerd.png # Enhanced E-R Database Diagram
│ └── crc_cards.pdf # Class Responsibility Cards
│
├── SQL/
│ └── UbuntuBay_db.sql # Database export script
│
├── uploads/
│ ├── products/ # Product images from vendors
│ └── profiles/ # User profile photos
│
├── index.php # Marketplace / Product Discovery Feed
├── login.php # Unified Authentication Gateway
├── logout.php # Destroys user session
├── register.php # New Account Registration
├── account.php # Dynamic Profile Hub (Buyer/Seller)
├── products.php # Product display with images/prices
├── profile.php # Real-time P2P communication
├── about.php # Platform vision & mission
├── checkout.php # Transaction processing
├── contact.php # Support & help desk
├── profile.php # User settings panel
└── delivery.php # Logistics (courier/pickup)

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| **Frontend** | HTML5, CSS3, JavaScript (jQuery), Bootstrap 5.3 |
| **Backend** | PHP 8 (Structured procedural/object-oriented) |
| **Database** | MySQL (Relational with foreign keys and cascade rules) |
| **Hosting** | InfinityFree / GitHub Pages |

---

## Live Site

*http://ubuntubayc2c.infinityfreeapp.com/index.php?i=1*

---

## Unified Customer Experience (Buyer & Seller Dual-State)

UbuntuBay eliminates complicated interface barriers by treating buyers and sellers identically under a unified "Customer" framework.

- **Role Fluidity:** Users dynamically navigate as buyer or seller.
- **Product Listing Engine:** Sellers input descriptions, prices (ZAR), inventory, and upload images
- **Real-time Inventory Management:** Database updates product quantity automatically upon confirmation
- **P2P Communication Hub (`profile.php`):** Direct localized chat between buyers and sellers on the messages tab

---

## Localized Logistics & Delivery Integration

Designed purposefully for localized township realities:

| Delivery Method | Description |
|----------------|-------------|
| **PikitUp Drop-off** | Low-cost, community-driven pickup centers for street vendors |
| **Integrated Courier** | Standard courier with R120 local SA shipping fee |
| **Cash-on-Delivery (CoD)** | Cash handovers during safe local pickups |

---

## Administrative Maintenance Panel (`/admin`)

| Feature | Description |
|---------|-------------|
| **System Metrics Dashboard** | Real-time operational statistics and marketplace volume |
| **User Accounts Moderation** | Manage customer accounts, handle disputes, flag suspicious accounts |
| **Listings Auditing** | Filter, review, or remove illegal listings to protect platform integrity |

---

## Getting Started

### Prerequisites
- XAMPP with PHP 8+ and MySQL
- Git (for version control)

### Local Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/KiashaHarilal/UbuntuBay.git
