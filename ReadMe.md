KasiKart: C2C E-Commerce Platform for the South African Informal Economy
KasiKart is a lightweight, mobile-responsive Consumer-to-Consumer (C2C) web application explicitly engineered to empower South African informal traders, street vendors, and township entrepreneurs. 
It bridges the gap between complex backend digital commerce and an intuitive, accessible user interface, allowing everyday users to safely list products, chat directly with local buyers, arrange localized shipping options, and update live inventory without requiring technical expertise.

Project Architecture & Directory Structure
The project code is organized cleanly into modular folders to guarantee separation of concerns, easy database configuration, and frictionless deployment to free cloud-hosting environments.

kasikart/


├── assets/
│   ├── css/
│   │   └── style.css           Custom UI styling, layout definitions, and Bootstrap overrides
│   ├── js/
│   │   └── script.js           Dynamic live-pricing calculation and front-end verification scripts
|   |   └── chat.js             Handles the client-side asynchronous logic (AJAX/Fetch API) to refresh chat strings, append user text inputs, and facilitate fast communication streams without requiring full page reloads.
│   └── images/                 Repository folder for static assets, system icons, and user upload storage
│
├── includes/
│   ├── header.php            Global application navbar, mobile toggle-menus, and user state headers
│   └── footer.php            Global sticky footer containing copyrights and compliance disclosures
|   └── database.php          Central MySQL database credentials and connection string
│
│
├── admin/                    Secure Administration Panel (Restricted via Role-Based Access Control)
│   ├── users.php             Control board to review profiles and moderate/suspend user entries
│   └── listings.php          Inventory audit board to moderate, remove, or modify active listings
|   └── index.php             The entry dashboard for administrators that checks admin security tokens and displays structural analytics, user sign-up velocity, and system-wide sales volumes.
|   └── login.php             A high-security authorization portal specifically assigned to verify admin operational access tokens before unlocking back-office rights.
|   └── logout.php            Instantly terminates administrative control tokens, invalidates backend management access rights, and returns the browser safely to the public login screen.
|   └── transactions.php      A master tracking ledger that allows administrators to audit platform transaction flows, monitor order completions, and identify marketplace abnormalities or payment disputes.
|    
│
├── diagrams/                 Deliverable 2 Design Diagrams (PNG / PDF formats)
│   ├── use_case.png          System Interaction Diagram
│   ├── context_diagram.png   DFD Level 0 Context Boundary Map
│   ├── dfd_level1.png        DFD Level 1 Detailed Process Sub-System Map
│   ├── eerd.png              Enhanced Entity Relationship Database Diagram
│   └── crc_cards.pdf         Class Responsibility Collaborator Cards Object Layout
│
├── index.php                 Platform Marketplace / Product & Service Discovery Feed
├── login.php                 Secure Unified Authentication Gateway
├── logout.php                Destroys the active user session, clears all authenticated global session variables, and securely redirects the user back to the public homepage or login screen.
├── register.php              New Account Registration Interface
├── account.php               Dynamic Profile Hub (Switch fluidly between Seller and Buyer tasks)
├── products.php              Product Focus Page displaying dynamic images, descriptions, and price values
├── chat.php                  Real-time peer-to-peer (P2P) localized communication screen
├── about.php                 A public informational page detailing the platform's vision, mission, and how it fosters the growth of South Africa's township C2C informal economy and local micro-businesses.
├── checkout.php              The transaction processing interface where buyers review selected products, choose cash or digital payment alternatives, and confirm their intention to purchase.
├── contact.php               A centralized communication portal containing support forms, operational contact information, and platform help-desk assistance lines for local merchants.
├── profile.php               The user-specific settings panel where both buyers and sellers update personal information, management locations, account security pins, and upload profile pictures.
└── delivery.php              Logistics Selection Page mapping out localized drop-off and courier routes
|
├── SQL/
│   ├── KasiKart_db.sql        The relational database export script containing complete relational table layouts (users, products, messages), entity rules, data constraints, and initial test data mock entries.
├── Uploads/
│   ├── products               A isolated repository where the backend stores image files uploaded by vendors whenever new items are listed
│   ├── profiles               The storage repository holding user profile photos and local identity images uploaded by customers to add platform accountability.

Frontend Framework: HTML5, CSS3, JavaScript (jQuery), and Bootstrap 5.3 (Loaded via CDN for responsive layout rendering across mobile phones, tablets, and desktops).
Backend Runtime Engine: PHP 8 (Structured procedural/object-oriented syntax processing dynamic system queries).
Database Management System: MySQL (Relational configuration managing transactional storage with foreign keys and cascade rules).
Target Live Hosting Environment: InfinityFree / GitHub Pages (Static hosting components mirror root configuration structures).


Live Site
https://kasikart.rf.gd  (replace with your actual InfinityFree URL)

Unified Customer Experience (Buyer & Seller Dual-State)
KasiKart eliminates complicated interface barriers by treating buyers and sellers identically under a unified "Customer" framework. Upon secure login via login.php:

Role Fluidity: A user can dynamically navigate as a buyer browsing the global feed (index.php), or switch states within account.php to access seller functionalities.
Product Listing Engine: Sellers can smoothly input item descriptions, specify dynamic prices (ZAR), set available item inventory, and upload product images.
Real-time Inventory Management: The system handles stock processing seamlessly. When an arrangement is confirmed via the workflow, the database updates the available product quantity automatically.
P2P Communication Hub (chat.php): Enables direct, localized chat coordinates between buyers and sellers to negotiate terms or schedule immediate rendezvous meetings without requiring public coordinate exposures.

Localized Logistics & Delivery Integration
Designed purposefully for localized township realities, the system details clear procedures on how users pay and receive products via delivery.php:
PikitUp Drop-off (Local Neighborhood Points): Low-cost, community-driven drop-off and pickup centers designed for street vendors operating within a close radius.
Integrated Courier Integration: Standard courier selection with an automatic R80 local South African shipping fee calculation rendered instantly using JavaScript logic.
Cash-on-Delivery (CoD) / Instant Transfer: Tailored payment flexibility allowing cash handovers during safe local pickups, matching traditional informal economic routines.

Administrative Maintenance Panel (/admin)
An isolated management core that handles back-office maintenance and database sanitization workflows:
System Metrics Dashboard: Provides administrators with real-time transparency into overall operational statistics and marketplace volume.
User Accounts Moderation: Oversees and manages customer accounts to handle disputes or flag suspicious accounts.
Listings Auditing and Compliance: Allows admins to filter, review, or forcefully pull down illegal listings, protecting platform integrity.
