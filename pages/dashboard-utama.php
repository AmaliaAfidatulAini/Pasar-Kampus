<?php
require_model('catalog');
$session = get_session();
$isLoggedIn = !empty($session);
$userName = $session['name'] ?? 'Pengguna';
$displayName = $session['name'] ?? $session['email'] ?? 'Pengguna';
$initial = strtoupper(substr($displayName, 0, 1));
$allProducts = get_catalog_products();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Katalog & Dashboard Utama</title>
    <!-- Google Fonts Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <style>
        :root {
            --pk-blue: #2355B8;
            --pk-blue-hover: #1b4598;
            --pk-navy: #1D356D;
            --pk-navy-dark: #15264F;
            --pk-footer: #1B3260;
            --pk-amber: #F59E0B;
            --pk-amber-dark: #D97706;
            --pk-primary: #2563EB;
            --pk-border: #E2E8F0;
            --pk-bg: #F8FAFC;
            --pk-text: #1E293B;
            --pk-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.pk-body {
            background-color: var(--pk-bg);
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--pk-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* 1. TOP NAVBAR */
        .pk-navbar {
            background-color: var(--pk-blue);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .pk-nav-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .pk-logo {
            font-size: 1.35rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            flex-shrink: 0;
        }

        .pk-nav-search {
            flex: 1;
            max-width: 440px;
            position: relative;
        }

        .pk-nav-search input {
            width: 100%;
            height: 38px;
            border-radius: 9999px;
            border: none;
            padding: 0 18px 0 40px;
            font-size: 0.85rem;
            outline: none;
            background: #FFFFFF;
            color: var(--pk-text);
            font-family: inherit;
        }

        .pk-nav-search input::placeholder {
            color: #94A3B8;
        }

        .pk-nav-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .pk-nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .pk-nav-link {
            color: #FFFFFF;
            font-size: 0.92rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .pk-nav-link:hover {
            opacity: 0.85;
        }

        .pk-btn-masuk {
            background-color: #FFFFFF;
            color: var(--pk-blue);
            font-weight: 700;
            font-size: 0.9rem;
            padding: 8px 22px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .pk-btn-masuk:hover {
            background-color: #F8FAFC;
            color: var(--pk-navy);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Profile Dropdown & Avatar */
        .lp-profile-wrapper {
            position: relative;
        }

        .lp-profile-btn {
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            outline: none;
        }

        .lp-profile-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #FFFFFF;
            border: 2px solid rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--pk-blue);
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            transition: all 0.2s ease;
        }

        .lp-profile-btn:hover .lp-profile-circle,
        .lp-profile-btn:focus .lp-profile-circle {
            border-color: #BFDBFE;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        .lp-avatar-initial {
            text-transform: uppercase;
        }

        .lp-profile-dropdown {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            width: 230px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.16), 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 8px 0;
            z-index: 1000;
            display: none;
            animation: lpDropdownPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .lp-profile-dropdown.show {
            display: block;
        }

        .lp-profile-dropdown::before {
            content: '';
            position: absolute;
            top: -7px;
            right: 14px;
            width: 12px;
            height: 12px;
            background: #FFFFFF;
            border-left: 1px solid #E2E8F0;
            border-top: 1px solid #E2E8F0;
            transform: rotate(45deg);
        }

        @keyframes lpDropdownPop {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .lp-dropdown-header {
            padding: 8px 18px 10px;
            border-bottom: 1px solid #F1F5F9;
        }

        .lp-dropdown-user-name {
            font-weight: 700;
            font-size: 0.92rem;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lp-dropdown-user-email {
            font-size: 0.78rem;
            color: #64748B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lp-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 18px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1E293B;
            text-decoration: none;
            background: transparent;
            border: none;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.15s ease;
        }

        .lp-dropdown-item:hover {
            background-color: #F8FAFC;
            color: #1D356D;
            padding-left: 22px;
        }

        .lp-dropdown-item svg {
            color: #64748B;
            flex-shrink: 0;
            transition: color 0.15s ease;
        }

        .lp-dropdown-item:hover svg {
            color: #1D356D;
        }

        .lp-dropdown-divider {
            height: 1px;
            background-color: #E2E8F0;
            margin: 6px 0;
        }

        .lp-dropdown-logout {
            color: #DC2626;
        }

        .lp-dropdown-logout svg {
            color: #DC2626;
        }

        .lp-dropdown-logout:hover {
            background-color: #FEF2F2;
            color: #B91C1C;
        }

        .lp-dropdown-logout:hover svg {
            color: #B91C1C;
        }

        /* 2. MAIN CONTAINER */
        .pk-main-wrapper {
            flex: 1;
            padding: 24px 24px 48px 24px;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .pk-content-container {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* 3. CATALOG LAYOUT */
        .pk-catalog-layout {
            display: flex;
            gap: 24px;
            align-items: flex-start;
            width: 100%;
        }

        .pk-catalog-main {
            flex: 1;
            min-width: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* 4. HERO BANNER */
        .pk-hero-banner {
            background-color: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 16px;
            padding: 28px 32px;
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            align-items: center;
            gap: 28px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            margin-bottom: 24px;
            width: 100%;
        }

        .pk-hero-left h1 {
            font-size: 1.95rem;
            font-weight: 800;
            line-height: 1.25;
            color: #0F172A;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .pk-hero-highlight {
            color: var(--pk-amber);
        }

        .pk-hero-desc {
            font-size: 0.86rem;
            color: var(--pk-muted);
            line-height: 1.55;
            margin-bottom: 20px;
            max-width: 440px;
        }

        .pk-hero-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pk-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--pk-primary);
            font-size: 0.76rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 9999px;
        }

        /* HERO RIGHT: FEATURED CARD */
        .pk-hero-right {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pk-featured-tag {
            border: 1.5px solid var(--pk-amber);
            color: var(--pk-amber-dark);
            font-size: 0.74rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #FFFBEB;
            letter-spacing: 0.2px;
        }

        .pk-hero-right .product-card {
            width: 100%;
            max-width: 260px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        /* 5. FILTER TOOLBAR */
        .pk-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 20px;
            min-height: 40px;
        }

        .pk-filter-btn {
            display: none;
        }

        .pk-toolbar-count {
            font-size: 0.84rem;
            font-weight: 600;
            color: #475569;
        }

        /* 6. PRODUCT GRID */
        .pk-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 36px;
        }

        .pk-grid .product-card {
            height: 100%;
        }

        .product-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease-in-out;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.08);
            border-color: #BFDBFE;
        }

        .product-card .product-img-wrapper {
            position: relative;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #F1F5F9;
        }

        .product-card .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-img-wrapper img {
            transform: scale(1.05);
        }

        .product-card .badge-condition {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--pk-text);
            color: #FFFFFF;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            z-index: 2;
        }

        .product-card .badge-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            color: #FFFFFF;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            z-index: 2;
        }

        .product-card .product-body {
            padding: 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card .product-location {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #1E40AF;
            background: #EFF6FF;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 8px;
            width: fit-content;
            border: 1px solid #BFDBFE;
        }

        .product-card .product-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--pk-text);
            line-height: 1.35;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .product-desc {
            font-size: 0.75rem;
            color: var(--pk-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .product-card .product-footer {
            padding: 12px 14px;
            border-top: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .product-card .product-price-box .orig-price {
            font-size: 0.72rem;
            color: #94A3B8;
            text-decoration: line-through;
            display: block;
        }

        .product-card .product-price-box .cur-price {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--pk-navy);
        }

        .product-card .btn-product-detail {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .product-card .btn-product-detail:hover {
            background-color: var(--pk-primary);
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        /* 7. PAGINATION */
        .pk-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0 10px;
        }

        .pk-page-btn {
            min-width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pk-text);
            cursor: pointer;
            transition: all 0.15s;
            background: transparent;
            border: none;
            padding: 0 6px;
            font-family: inherit;
        }

        .pk-page-btn:hover {
            background: #E2E8F0;
        }

        .pk-page-btn.active {
            background: var(--pk-primary);
            color: #FFFFFF;
            border-radius: 9999px;
            width: 34px;
            height: 34px;
        }

        /* 8. FILTER SIDEBAR */
        .pk-sidebar-backdrop {
            display: none;
        }

        .pk-sidebar-drawer {
            width: 270px;
            min-width: 270px;
            max-width: 270px;
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            position: sticky;
            top: 84px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 10;
        }

        .pk-sidebar-header {
            background-color: var(--pk-blue);
            height: 52px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .pk-sidebar-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #FBBF24;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .pk-sidebar-close {
            display: none;
        }

        .pk-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pk-sidebar-body::-webkit-scrollbar {
            width: 5px;
        }

        .pk-sidebar-body::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        .pk-filter-group-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--pk-navy);
            letter-spacing: 0.3px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .pk-filter-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pk-filter-item {
            font-size: 0.84rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 6px;
            transition: all 0.15s;
            user-select: none;
        }

        .pk-filter-item:hover {
            color: var(--pk-primary);
            background: #F1F5F9;
        }

        .pk-filter-item.active {
            color: var(--pk-primary);
            font-weight: 700;
            background: #EFF6FF;
        }

        /* Styling interaktif untuk label kondisi */
        .cond-label {
            cursor: pointer;
            color: #94A3B8;
            font-weight: 600;
            transition: all 0.2s ease;
            user-select: none;
        }
        .cond-label:hover {
            color: var(--pk-amber-dark);
        }
        .cond-label.active {
            color: var(--pk-amber-dark);
            font-weight: 800;
        }

        .pk-see-more {
            font-size: 0.78rem;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            user-select: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .pk-see-more:hover {
            color: var(--pk-primary);
        }

        .pk-checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pk-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }

        .pk-checkbox-item input[type="checkbox"] {
            width: 17px;
            height: 17px;
            border: 1.5px solid #CBD5E1;
            border-radius: 4px;
            cursor: pointer;
            accent-color: var(--pk-navy);
        }

        .pk-price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .pk-price-box label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 5px;
        }

        .pk-price-box input {
            width: 100%;
            height: 36px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 0 8px;
            font-size: 0.8rem;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pk-price-box input:focus {
            border-color: var(--pk-primary);
        }

        .pk-sidebar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 4px;
        }

        .pk-reset-btn {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.2s;
            white-space: nowrap;
        }

        .pk-reset-btn:hover {
            background-color: var(--pk-navy-dark);
        }

        .pk-apply-btn {
            display: none;
        }

        /* 9. FOOTER */
        .pk-footer {
            background-color: var(--pk-footer);
            color: #CBD5E1;
            padding: 48px 24px 24px;
            margin-top: auto;
        }

        .pk-footer-inner {
            max-width: 1440px;
            margin: 0 auto;
        }

        .pk-footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 36px;
            margin-bottom: 36px;
        }

        .pk-footer-col h4 {
            color: #FBBF24;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .pk-footer-col p {
            font-size: 0.84rem;
            line-height: 1.6;
            color: #94A3B8;
            max-width: 320px;
        }

        .pk-footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pk-footer-links a {
            font-size: 0.84rem;
            color: #CBD5E1;
            transition: color 0.15s;
        }

        .pk-footer-links a:hover {
            color: #FFFFFF;
        }

        .pk-footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: #94A3B8;
            flex-wrap: wrap;
            gap: 12px;
        }

        @media (min-width: 992px) and (max-width: 1180px) {
            .pk-hero-banner {
                grid-template-columns: 1fr 1fr;
                padding: 24px 22px;
                gap: 20px;
            }

            .pk-hero-left h1 {
                font-size: 1.65rem;
            }

            .pk-hero-right .product-card {
                max-width: 235px;
            }

            .pk-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
            }
        }

        @media (max-width: 991px) {
            .pk-main-wrapper {
                padding: 16px 16px 36px;
            }

            .pk-catalog-layout {
                display: block;
            }

            .pk-catalog-main {
                width: 100%;
            }

            .pk-sidebar-backdrop {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(15, 23, 42, 0.45);
                backdrop-filter: blur(2px);
                z-index: 998;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }

            .pk-sidebar-backdrop.active {
                opacity: 1;
                pointer-events: auto;
            }

            .pk-sidebar-drawer {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 320px;
                max-width: 85vw;
                height: 100vh;
                background: #FFFFFF;
                z-index: 999;
                box-shadow: 10px 0 30px rgba(15, 23, 42, 0.2);
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
                border-radius: 0;
                border: none;
                overflow: hidden;
            }

            .pk-sidebar-drawer.active {
                transform: translateX(0);
            }

            .pk-sidebar-header {
                height: 56px;
                padding: 0 18px;
                border-radius: 0;
            }

            .pk-sidebar-header .pk-sidebar-close {
                display: block !important;
                background: none;
                border: none;
                font-size: 1.6rem;
                color: #FBBF24;
                cursor: pointer;
                padding: 4px 8px;
                line-height: 1;
            }

            .pk-apply-btn {
                display: block !important;
                background-color: var(--pk-primary);
                color: #FFFFFF;
                border: none;
                border-radius: 8px;
                padding: 9px 16px;
                font-size: 0.82rem;
                font-weight: 700;
                cursor: pointer;
                flex: 1;
                text-align: center;
            }

            .pk-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin: 0 0 16px;
                min-height: 42px;
            }

            .pk-filter-btn {
                display: inline-flex !important;
                align-items: center;
                gap: 8px;
                background: #FFFFFF;
                border: 1.5px solid var(--pk-border);
                border-radius: 10px;
                padding: 8px 16px;
                font-size: 0.85rem;
                font-weight: 700;
                color: var(--pk-navy);
                cursor: pointer;
            }

            .pk-hero-banner {
                grid-template-columns: 1fr;
                padding: 24px 20px;
                gap: 22px;
                border-radius: 16px;
                margin-bottom: 20px;
            }

            .pk-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }

        @media (max-width: 768px) {
            .pk-navbar {
                height: auto;
                padding: 10px 16px;
            }

            .pk-nav-inner {
                flex-wrap: wrap;
                gap: 10px;
            }

            .pk-nav-search {
                order: 3;
                width: 100%;
                max-width: 100%;
            }

            .pk-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .pk-footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }
        }

        @media (max-width: 640px) {
            .pk-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .pk-footer-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 380px) {
            .pk-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>
<body class="pk-body">

    <!-- ================= 1. TOP NAVBAR ================= -->
    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <a href="<?= route('/dashboard-utama.php') ?>" class="pk-logo">
                PasarKampus
            </a>

            <div class="pk-nav-search">
                <span class="pk-nav-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" id="dashSearch" oninput="applyCatalogFilters()" placeholder="Cari barang....">
            </div>

            <nav class="pk-nav-links">
                <?php if ($isLoggedIn): ?>
                    <div class="lp-profile-wrapper">
                        <button type="button" id="lpProfileBtn" class="lp-profile-btn" onclick="toggleProfileDropdown(event)" aria-haspopup="true" aria-expanded="false" title="Menu Profil">
                            <div class="lp-profile-circle" id="lpAvatarCircle" style="<?= !empty($session['avatar_url']) ? 'padding:0; overflow:hidden;' : '' ?>">
                                <?php if (!empty($session['avatar_url'])): ?>
                                    <img src="<?= esc($session['avatar_url']) ?>" alt="Avatar" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                                <?php else: ?>
                                    <span id="lpAvatarInitial" class="lp-avatar-initial"><?= esc($initial) ?></span>
                                <?php endif; ?>
                            </div>
                        </button>

                        <div id="lpProfileDropdown" class="lp-profile-dropdown">
                            <div class="lp-dropdown-header">
                                <div class="lp-dropdown-user-name"><?= esc($displayName) ?></div>
                                <div class="lp-dropdown-user-email"><?= esc($session['email'] ?? '') ?></div>
                            </div>
                            <div class="lp-dropdown-divider"></div>
                            <a href="<?= route('/dashboard_penjual.php') ?>" class="lp-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <span>Daftar Barang</span>
                            </a>
                            <a href="<?= route('/pengajuan.php') ?>" class="lp-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span>Riwayat Pengajuan</span>
                            </a>
                            <a href="<?= route('/profile') ?>" class="lp-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil Pengguna</span>
                            </a>
                            <div class="lp-dropdown-divider"></div>
                            <a href="<?= route('/logout') ?>" class="lp-dropdown-item lp-dropdown-logout" onclick="return confirm('Apakah Anda yakin ingin keluar dari akun PasarKampus?');">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= route('/login') ?>" class="pk-btn-masuk">Masuk</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- ================= 2. MAIN DASHBOARD CONTENT ================= -->
    <main class="pk-main-wrapper">
        <div class="pk-content-container">

            <!-- Backdrop for Mobile Drawer -->
            <div class="pk-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

            <!-- ================= 3. TWO-FRAME SPLIT LAYOUT ================= -->
            <div class="pk-catalog-layout">

                <!-- SIDEBAR FILTER -->
                <aside class="pk-sidebar-drawer" id="sidebarDrawer" aria-label="Filter Barang">
                    <div class="pk-sidebar-header">
                        <span class="pk-sidebar-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                            FILTER
                        </span>
                        <button type="button" class="pk-sidebar-close" onclick="closeSidebar()" title="Tutup Filter">&times;</button>
                    </div>

                    <div class="pk-sidebar-body">
                        <!-- 1. KATEGORI -->
                        <div>
                            <div class="pk-filter-group-title">KATEGORI</div>
                            <ul class="pk-filter-list" id="sidebarCatList">
                                <li class="pk-filter-item" onclick="selectCategory('Buku & Akademik', this)">Buku &amp; Akademik</li>
                                <li class="pk-filter-item" onclick="selectCategory('Perlengkapan Kos', this)">Perlengkapan Kos</li>
                                <li class="pk-filter-item" onclick="selectCategory('Fashion & Apparel', this)">Fashion &amp; Apparel</li>
                                <li class="pk-filter-item" onclick="selectCategory('Elektronik', this)">Elektronik</li>
                            </ul>
                            <div class="pk-see-more" onclick="toggleExpandList('sidebarCatList', this)">Lihat semua &#x25BE;</div>
                        </div>

                        <!-- 2. TAGS -->
                        <div>
                            <div class="pk-filter-group-title">TAGS</div>
                            <ul class="pk-filter-list" id="sidebarTagList">
                                <li class="pk-filter-item" onclick="selectTag('#ModulKuliah', this)">#ModulKuliah</li>
                                <li class="pk-filter-item" onclick="selectTag('#JasAlmamater', this)">#JasAlmamater</li>
                                <li class="pk-filter-item" onclick="selectTag('#MejaBelajar', this)">#MejaBelajar</li>
                                <li class="pk-filter-item" onclick="selectTag('#Jaket', this)">#Jaket</li>
                            </ul>
                            <div class="pk-see-more" onclick="toggleExpandList('sidebarTagList', this)">Lihat semua &#x25BE;</div>
                        </div>

                        <!-- 3. LOKASI -->
                        <div>
                            <div class="pk-filter-group-title">LOKASI</div>
                            <div class="pk-checkbox-group" id="sidebarLocGroup">
                                <label class="pk-checkbox-item">
                                    <input type="checkbox" name="locFilter" value="Area Universitas Brawijaya" onchange="applyCatalogFilters()">
                                    <span>Area Universitas Brawijaya</span>
                                </label>
                                <label class="pk-checkbox-item">
                                    <input type="checkbox" name="locFilter" value="Area Universitas Negeri Malang" onchange="applyCatalogFilters()">
                                    <span>Area Universitas Negeri Malang</span>
                                </label>
                                <label class="pk-checkbox-item">
                                    <input type="checkbox" name="locFilter" value="Area Politeknik Negeri Malang" onchange="applyCatalogFilters()">
                                    <span>Area Politeknik Negeri Malang</span>
                                </label>
                                <label class="pk-checkbox-item">
                                    <input type="checkbox" name="locFilter" value="Area Universitas Terbuka" onchange="applyCatalogFilters()">
                                    <span>Area Universitas Terbuka</span>
                                </label>
                            </div>
                            <div class="pk-see-more" onclick="toggleExpandList('sidebarLocGroup', this)">Lihat semua &#x25BE;</div>
                        </div>

                        <!-- 4. RENTANG HARGA -->
                        <div>
                            <div class="pk-filter-group-title">RENTANG HARGA</div>
                            <div class="pk-price-row">
                                <div class="pk-price-box">
                                    <label for="priceMinInput">Rp. Min</label>
                                    <input type="number" id="priceMinInput" placeholder="" oninput="applyCatalogFilters()">
                                </div>
                                <div class="pk-price-box">
                                    <label for="priceMaxInput">Rp. Max</label>
                                    <input type="number" id="priceMaxInput" placeholder="" oninput="applyCatalogFilters()">
                                </div>
                            </div>
                        </div>

                        <!-- 5. KONDISI BARANG (Slider + Label Interaktif 2 Arah) -->
                        <div>
                            <div class="pk-filter-group-title">KONDISI BARANG</div>
                            <div style="padding: 4px 0 8px;">
                                <input type="range" id="conditionRangeInput" min="0" max="3" value="0" step="1" style="width: 100%; accent-color: var(--pk-navy); cursor: pointer;" oninput="onConditionChange()">
                                <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.68rem;">
                                    <span class="cond-label" id="condLabel0" onclick="setConditionRange(0)">Bekas</span>
                                    <span class="cond-label" id="condLabel1" onclick="setConditionRange(1)">Cukup</span>
                                    <span class="cond-label" id="condLabel2" onclick="setConditionRange(2)">Baik</span>
                                    <span class="cond-label" id="condLabel3" onclick="setConditionRange(3)">Sangat bagus</span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. ACTION BUTTONS -->
                        <div class="pk-sidebar-actions">
                            <button type="button" class="pk-reset-btn" onclick="resetSidebarFilters()">Reset all</button>
                            <button type="button" class="pk-apply-btn" onclick="closeSidebar()">Lihat Hasil</button>
                        </div>
                    </div>
                </aside>

                <!-- RIGHT FRAME: HERO BANNER + CATALOG MAIN CONTENT -->
                <div class="pk-catalog-main">

                    <!-- Hero Banner -->
                    <section class="pk-hero-banner">
                        <div class="pk-hero-left">
                            <h1>
                                Cari Barang Bekas<br>
                                <span class="pk-hero-highlight">Berkualitas</span> &amp; Murah!
                            </h1>
                            <p class="pk-hero-desc">
                                Temukan buku kuliah, elektronik, perlengkapan kost, ATK, hingga pakaian preloved dari teman sekampus dan sekitar domisili Anda dengan aman &amp; mudah.
                            </p>
                            <div class="pk-hero-pills">
                                <div class="pk-pill-badge">🎓 100% Khusus Mahasiswa</div>
                                <div class="pk-pill-badge">🤝 Aman COD Area Kampus</div>
                                <div class="pk-pill-badge">🏷️ Harga terjangkau</div>
                            </div>
                        </div>

                        <div class="pk-hero-right" style="display: flex; flex-direction: column; align-items: center;">
                            <span class="pk-featured-tag">Baru Diunggah</span>
                            <?php
                            $featuredItem = $allProducts[0] ?? [
                                'id' => 1,
                                'title' => 'Keyboard Logiac - Silver',
                                'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..',
                                'category' => 'Elektronik',
                                'location' => 'Kecamatan Lowokwaru',
                                'condition' => 'Layak pakai',
                                'original_price' => 150000,
                                'price' => 90000,
                                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                            ];
                            Component::include('card', [
                                'id' => $featuredItem['id'],
                                'title' => $featuredItem['title'],
                                'description' => $featuredItem['short_desc'] ?? $featuredItem['description'] ?? '',
                                'category' => $featuredItem['category'] ?? '',
                                'location' => $featuredItem['location'] ?? '',
                                'condition' => $featuredItem['condition'] ?? '',
                                'o_price' => $featuredItem['original_price'] ?? '',
                                'price' => $featuredItem['price'] ?? '',
                                'foto' => $featuredItem['image'] ?? '',
                            ]);
                            ?>
                        </div>
                    </section>

                    <!-- Toolbar -->
                    <div class="pk-toolbar">
                        <button type="button" class="pk-filter-btn" onclick="openSidebar()" title="Buka Filter">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="pk-toolbar-count" id="itemCountDisplay">
                            Menampilkan 1-20 dari 66 barang
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="pk-grid" id="productGrid">
                        <?php foreach (array_slice($allProducts, 0, 12) as $item): ?>
                            <?php
                            Component::include('card', [
                                'id' => $item['id'],
                                'title' => $item['title'],
                                'description' => $item['short_desc'] ?? $item['description'] ?? '',
                                'category' => $item['category'] ?? '',
                                'location' => $item['location'] ?? '',
                                'condition' => $item['condition'] ?? '',
                                'o_price' => $item['original_price'] ?? '',
                                'price' => $item['price'] ?? '',
                                'foto' => $item['image'] ?? '',
                            ]);
                            ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Empty State -->
                    <div id="gridEmpty" style="display: none; text-align: center; padding: 48px 20px; background: #FFF; border-radius: 14px; border: 1px dashed var(--pk-border); margin: 20px 0;">
                        <div style="font-size: 2.5rem; margin-bottom: 8px;">📦</div>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--pk-navy); margin: 0 0 6px 0;">Tidak ada barang yang cocok</h3>
                        <p style="font-size: 0.84rem; color: #64748B; margin: 0 0 16px 0;">Coba atur ulang filter pencarian atau gunakan kata kunci lain.</p>
                        <button type="button" onclick="resetSidebarFilters()" class="pk-reset-btn" style="align-self: center;">Reset Semua Filter</button>
                    </div>

                    <!-- Pagination -->
                    <div class="pk-pagination" id="paginationBar">
                        <button type="button" class="pk-page-btn" onclick="changePage('prev')">&lt;</button>
                        <button type="button" class="pk-page-btn active" onclick="setPage(1)">1</button>
                        <button type="button" class="pk-page-btn" onclick="setPage(2)">2</button>
                        <button type="button" class="pk-page-btn" onclick="setPage(3)">3</button>
                        <button type="button" class="pk-page-btn" onclick="setPage(4)">4</button>
                        <button type="button" class="pk-page-btn" onclick="setPage(5)">5</button>
                        <button type="button" class="pk-page-btn" onclick="changePage('next')">&gt;</button>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- ================= 4. FOOTER ================= -->
    <footer class="pk-footer">
        <div class="pk-footer-inner">
            <div class="pk-footer-grid">
                <div class="pk-footer-col">
                    <h3 style="color: #FFFFFF; font-size: 1.35rem; font-weight: 800; margin-bottom: 12px;">PasarKampus</h3>
                    <p>Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.</p>
                </div>
                <div class="pk-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="pk-footer-links">
                        <li><a href="<?= route('/') ?>">Tentang kami</a></li>
                        <li><a href="<?= route('/dashboard-utama.php') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register.php') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/dashboard-penjual.php') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#">Pusat Bantuan</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#">Kebijakan</a></li>
                        <li><a href="#">Panduan Keamanan COD</a></li>
                        <li><a href="#">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="pk-footer-bottom">
                <div>&copy; <?= date('Y') ?> PasarKampus, Hak Cipta Dilindungi.</div>
                <div>Dibuat untuk Project Based Learning (PBL) Mahasiswa</div>
            </div>
        </div>
    </footer>

    <!-- ================= JAVASCRIPT LOGIC ================= -->
    <script>
        const rawProducts = <?= json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        
        let selectedCategory = '';
        let selectedTag = '';
        let conditionActive = false; // Status apakah filter kondisi sedang aktif digunakan
        let currentPage = 1;
        const pageSize = 12;

        function formatRupiah(num) {
            return 'Rp. ' + Number(num).toLocaleString('id-ID');
        }

        function openSidebar() {
            const drawer = document.getElementById('sidebarDrawer');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (drawer) drawer.classList.add('active');
            if (backdrop) backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            const drawer = document.getElementById('sidebarDrawer');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (drawer) drawer.classList.remove('active');
            if (backdrop) backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

        function selectCategory(cat, element) {
            if (selectedCategory === cat) {
                selectedCategory = '';
                element.classList.remove('active');
            } else {
                selectedCategory = cat;
                document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
            }
            applyCatalogFilters();
        }

        function selectTag(tag, element) {
            if (selectedTag === tag) {
                selectedTag = '';
                element.classList.remove('active');
            } else {
                selectedTag = tag;
                document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
            }
            applyCatalogFilters();
        }

        function updateConditionLabels(val) {
            document.querySelectorAll('.cond-label').forEach((el, idx) => {
                if (idx === parseInt(val)) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
        }

        function onConditionChange() {
            conditionActive = true;
            const val = document.getElementById('conditionRangeInput')?.value || 0;
            updateConditionLabels(val);
            applyCatalogFilters();
        }

        function setConditionRange(val) {
            const slider = document.getElementById('conditionRangeInput');
            if (slider) {
                slider.value = val;
                conditionActive = true;
                updateConditionLabels(val);
                applyCatalogFilters();
            }
        }

        function toggleExpandList(listId, toggleBtn) {
            const list = document.getElementById(listId);
            if (!list) return;
            if (toggleBtn.innerText.includes('▼')) {
                toggleBtn.innerText = 'Tutup ▲';
            } else {
                toggleBtn.innerText = 'Lihat semua ▼';
            }
        }

        function resetSidebarFilters() {
            selectedCategory = '';
            selectedTag = '';
            conditionActive = false;
            document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.cond-label').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('input[name="locFilter"]').forEach(cb => cb.checked = false);
            
            const condEl = document.getElementById('conditionRangeInput');
            if (condEl) condEl.value = 0;

            const minEl = document.getElementById('priceMinInput');
            const maxEl = document.getElementById('priceMaxInput');
            if (minEl) minEl.value = '';
            if (maxEl) maxEl.value = '';
            
            const searchEl = document.getElementById('dashSearch');
            if (searchEl) searchEl.value = '';

            currentPage = 1;
            applyCatalogFilters();
        }

        function applyCatalogFilters() {
            const searchVal = (document.getElementById('dashSearch')?.value || '').toLowerCase().trim();
            const minPrice = parseFloat(document.getElementById('priceMinInput')?.value) || 0;
            const maxPrice = parseFloat(document.getElementById('priceMaxInput')?.value) || Infinity;

            const checkedLocations = Array.from(document.querySelectorAll('input[name="locFilter"]:checked'))
                .map(cb => cb.value.toLowerCase());
            
            const conditionVal = parseInt(document.getElementById('conditionRangeInput')?.value) || 0;

            const filtered = rawProducts.filter(item => {
                // Category match
                if (selectedCategory && item.category.toLowerCase() !== selectedCategory.toLowerCase()) {
                    return false;
                }

                // Tag match
                if (selectedTag && !item.tags.map(t => t.toLowerCase()).includes(selectedTag.toLowerCase())) {
                    return false;
                }

                // Location match
                if (checkedLocations.length > 0) {
                    const itemLoc = (item.campus || '').toLowerCase();
                    const hasMatch = checkedLocations.some(loc => itemLoc.includes(loc) || loc.includes(itemLoc));
                    if (!hasMatch) return false;
                }

                // Price range match
                if (item.price < minPrice || item.price > maxPrice) {
                    return false;
                }
                
                // Condition match
                if (conditionActive) {
                    const itemCond = (item.condition || '').toLowerCase();
                    if (conditionVal === 0 && !itemCond.includes('bekas')) {
                        return false;
                    }
                    if (conditionVal === 1 && !itemCond.includes('cukup')) {
                        return false;
                    }
                    if (conditionVal === 2 && !itemCond.includes('baik')) {
                        return false;
                    }
                    if (conditionVal === 3 && !itemCond.includes('sangat bagus')) {
                        return false;
                    }
                }

                // Keyword search match
                if (searchVal) {
                    const inTitle = item.title.toLowerCase().includes(searchVal);
                    const inDesc = (item.description || '').toLowerCase().includes(searchVal);
                    const inLoc = (item.location || '').toLowerCase().includes(searchVal);
                    const inCampus = (item.campus || '').toLowerCase();
                    const inTags = (item.tags || []).some(t => t.toLowerCase().includes(searchVal));
                    if (!inTitle && !inDesc && !inLoc && !inCampus && !inTags) {
                        return false;
                    }
                }

                return true;
            });

            renderGrid(filtered);
        }

        function renderGrid(items) {
            const grid = document.getElementById('productGrid');
            const empty = document.getElementById('gridEmpty');
            const countDisplay = document.getElementById('itemCountDisplay');
            const paginationBar = document.getElementById('paginationBar');

            if (!grid) return;

            if (items.length === 0) {
                grid.innerHTML = '';
                if (empty) empty.style.display = 'block';
                if (countDisplay) countDisplay.innerText = 'Menampilkan 0 barang';
                if (paginationBar) paginationBar.style.display = 'none';
                return;
            }

            if (empty) empty.style.display = 'none';
            if (paginationBar) paginationBar.style.display = 'flex';
            if (countDisplay) {
                countDisplay.innerText = `Menampilkan 1-${Math.min(items.length, 20)} dari ${Math.max(items.length, 66)} barang`;
            }

            const startIdx = (currentPage - 1) * pageSize;
            const pageItems = items.slice(startIdx, startIdx + pageSize);

            grid.innerHTML = pageItems.map(item => `
                <div class="product-card" onclick="if (!event.target.closest('a')) goToProductDetail(${item.id})" style="cursor: pointer;">
                    <div>
                        <div class="product-img-wrapper">
                            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" loading="lazy">
                            <span class="badge-condition">${escapeHtml(item.condition || 'Layak pakai')}</span>
                            <span class="badge-category">${escapeHtml(item.category || 'Umum')}</span>
                        </div>
                        <div class="product-body">
                            <div class="product-location">${escapeHtml((item.location && item.location.startsWith('📍')) ? item.location : ('📍 ' + (item.location || 'Kecamatan Lowokwaru')))}</div>
                            <h3 class="product-title">${escapeHtml(item.title)}</h3>
                            <p class="product-desc">${escapeHtml(item.short_desc || item.description || '')}</p>
                        </div>
                    </div>
                    <div class="product-footer">
                        <div class="product-price-box">
                            ${item.original_price ? `<span class="orig-price">Rp. ${Number(item.original_price).toLocaleString('id-ID')}</span>` : ''}
                            <div class="cur-price">Rp. ${Number(item.price).toLocaleString('id-ID')}</div>
                        </div>
                        <a href="<?= route('/barang') ?>?id=${encodeURIComponent(item.id)}" class="btn-product-detail">
                            Detail
                        </a>
                    </div>
                </div>
            `).join('');
        }

        function goToProductDetail(id) {
            window.location.href = `<?= route('/barang') ?>?id=${encodeURIComponent(id)}`;
        }

        function setPage(pageNum) {
            currentPage = pageNum;
            document.querySelectorAll('#paginationBar .pk-page-btn').forEach(btn => {
                if (btn.innerText.trim() === String(pageNum)) {
                    btn.classList.add('active');
                } else if (!['<', '>'].includes(btn.innerText.trim())) {
                    btn.classList.remove('active');
                }
            });
            applyCatalogFilters();
            window.scrollTo({ top: 380, behavior: 'smooth' });
        }

        function changePage(direction) {
            if (direction === 'prev' && currentPage > 1) {
                setPage(currentPage - 1);
            } else if (direction === 'next' && currentPage < 5) {
                setPage(currentPage + 1);
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function toggleProfileDropdown(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (!dropdown) return;

            const isShown = dropdown.classList.contains('show');
            dropdown.classList.toggle('show', !isShown);
            if (btn) btn.setAttribute('aria-expanded', isShown ? 'false' : 'true');
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (dropdown) dropdown.classList.remove('show');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }

        document.addEventListener('click', (event) => {
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (dropdown && dropdown.classList.contains('show')) {
                if (!dropdown.contains(event.target) && (!btn || !btn.contains(event.target))) {
                    closeProfileDropdown();
                }
            }
        });
    </script>
</body>
</html>