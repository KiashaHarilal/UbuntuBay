# KasiKart: C2C E-Commerce Platform for the South African Informal Economy

KasiKart is a lightweight, mobile-responsive Consumer-to-Consumer (C2C) web application explicitly engineered to empower South African informal traders, street vendors, and township entrepreneurs. It bridges the gap between complex backend digital commerce and an intuitive, accessible user interface, allowing everyday users to safely list products, chat directly with local buyers, arrange localized shipping options, and update live inventory without requiring technical expertise.

---

## Project Architecture & Directory Structure

The project code is organized cleanly into modular folders to guarantee separation of concerns, easy database configuration, and frictionless deployment to free cloud-hosting environments.
kasikart/
│
├── assets/
│ ├── css/
│ │ └── style.css Custom UI styling, layout definitions, and Bootstrap overrides
│ ├── js/
│ │ ├── script.js Dynamic live-pricing calculation and front-end verification scripts
│ │ └── chat.js Handles client-side AJAX/Fetch API for real-time chat
│ └── images/ Static assets, system icons, and user upload storage
│
├── includes/
│ ├── header.php Global application navbar, mobile toggle-menus
│ ├── footer.php Global sticky footer with copyrights
│ └── database.php Central MySQL database credentials and connection string
│
├── admin/ Secure Administration Panel (RBAC Protected)
│ ├── index.php Entry dashboard with admin security tokens and analytics
│ ├── login.php High-security authorization portal for admin access
│ ├── logout.php Terminates administrative control tokens
│ ├── users.php Review profiles and moderate/suspend user entries
│ ├── listings.php Inventory audit board to moderate active listings
│ └── transactions.php Master tracking ledger for transaction flows
│
├── diagrams/ Deliverable 2 Design Diagrams
│ ├── use_case.png System Interaction Diagram
│ ├── context_diagram.png DFD Level 0 Context Boundary Map
│ ├── dfd_level1.png DFD Level 1 Detailed Process Map
│ ├── eerd.png Enhanced Entity Relationship Database Diagram
│ └── crc_cards.pdf Class Responsibility Collaborator Cards
│
├── index.php Platform Marketplace / Product Discovery Feed
├── login.php Secure Unified Authentication Gateway
├── logout.php Destroys active user session
├── register.php New Account Registration Interface
├── account.php Dynamic Profile Hub (Buyer/Seller switch)
├── products.php Product Focus Page with images, descriptions, prices
├── chat.php Real-time peer-to-peer communication screen
├── about.php Platform vision, mission, and impact info
├── checkout.php Transaction processing interface
├── contact.php Centralized communication portal with support forms
├── profile.php User settings panel for personal information
├── delivery.php Logistics Selection Page for courier/pickup
│
├── SQL/
│ └── KasiKart_db.sql Relational database export script
│
└── uploads/
├── products/ Product images uploaded by vendors
└── profiles/ User profile photos for accountability


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

🔗 **https://kasikart.rf.gd**

---

## Unified Customer Experience (Buyer & Seller Dual-State)

KasiKart eliminates complicated interface barriers by treating buyers and sellers identically under a unified "Customer" framework.

- **Role Fluidity:** Users dynamically navigate as buyer or seller within `account.php`
- **Product Listing Engine:** Sellers input descriptions, prices (ZAR), inventory, and upload images
- **Real-time Inventory Management:** Database updates product quantity automatically upon confirmation
- **P2P Communication Hub (`chat.php`):** Direct localized chat between buyers and sellers

---

## Localized Logistics & Delivery Integration

Designed purposefully for localized township realities:

| Delivery Method | Description |
|----------------|-------------|
| **PikitUp Drop-off** | Low-cost, community-driven pickup centers for street vendors |
| **Integrated Courier** | Standard courier with R80 local SA shipping fee |
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
- XAMPP / WAMP with PHP 8+ and MySQL
- Git (for version control)

### Local Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/KiashaHarilal/KasiKart.git
