@extends('layouts.app')

@section('title', $course->title . ' - منصة تعليمية')

@section('styles')
<style>
    /* CSS Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Variables globales */
    :root {
        --primary-color: #003366;
        --primary-dark: #002244;
        --primary-light: #E6F0FF;
        --secondary-color: #FFD700;
        --secondary-dark: #FFC000;
        --accent-color: #FF6B6B;
        --text-color: #333;
        --text-light: #666;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
        --dark-gray: #6c757d;
        --success-color: #2ECC71;
        --warning-color: #F39C12;
        --danger-color: #E74C3C;
        --white: #fff;
        --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        --animation-slow: 0.7s;
        --animation-medium: 0.5s;
        --animation-fast: 0.3s;
        --border-radius-sm: 5px;
        --border-radius-md: 10px;
        --border-radius-lg: 15px;
        --border-radius-xl: 30px;
        --font-heading: 'Tajawal', 'Cairo', sans-serif;
        --font-body: 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;
        --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        --gradient-secondary: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-dark) 100%);
    }

    /* Layout base */
    body {
        font-family: var(--font-body);
        line-height: 1.6;
        color: var(--text-color);
        background-color: #f5f7fa;
        overflow-x: hidden;
        width: 100%;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-heading);
        font-weight: 700;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        box-sizing: border-box;
    }

    /* Grid System */
    .course-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        width: 100%;
    }

    @media (min-width: 992px) {
        .course-grid {
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
    }

    /* Utility Classes */
    .flex {
        display: flex;
    }

    .flex-between {
        justify-content: space-between;
    }

    .flex-center {
        align-items: center;
    }

    .flex-column {
        flex-direction: column;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }

    .gap-10 {
        gap: 10px;
    }

    .gap-20 {
        gap: 20px;
    }

    .text-center {
        text-align: center;
    }

    .mb-10 {
        margin-bottom: 10px;
    }

    .mb-20 {
        margin-bottom: 20px;
    }

    .mb-30 {
        margin-bottom: 30px;
    }

    .mb-50 {
        margin-bottom: 50px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .mt-20 {
        margin-top: 20px;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .mt-50 {
        margin-top: 50px;
    }

    .p-20 {
        padding: 20px;
    }

    .p-30 {
        padding: 30px;
    }

    /* Global Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-50px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes floating {
        0% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0); }
    }

    /* Animation Base Styles */
    .animated {
        animation-duration: var(--animation-medium);
        animation-fill-mode: both;
    }

    @media (prefers-reduced-motion: reduce) {
        .animated {
            animation: none !important;
            transition: none !important;
        }
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }

    .fade-in { animation-name: fadeIn; }
    .slide-in-left { animation-name: slideInLeft; }
    .slide-in-right { animation-name: slideInRight; }

    /* Course Header */
    .course-header {
        background: var(--gradient-primary);
        padding: 2.5rem 0 1.875rem;
        color: var(--white);
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border-bottom: 0.3125rem solid var(--secondary-color);
        width: 100%;
    }

    @media (min-width: 576px) {
        .course-header {
            padding: 3.75rem 0 1.875rem;
            margin-bottom: 1.5625rem;
        }
    }

    @media (min-width: 768px) {
        .course-header {
            padding: 5rem 0 2.5rem;
            margin-bottom: 1.875rem;
        }
    }

    .course-header::before {
        content: '';
        position: absolute;
        top: -3.125rem;
        right: -3.125rem;
        width: 6.25rem;
        height: 6.25rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 1;
        animation: floating var(--animation-slow) infinite ease-in-out;
    }

    @media (min-width: 576px) {
        .course-header::before {
            width: 9.375rem;
            height: 9.375rem;
        }
    }

    @media (min-width: 768px) {
        .course-header::before {
            width: 12.5rem;
            height: 12.5rem;
        }
    }

    .course-header::after {
        content: '';
        position: absolute;
        bottom: -5rem;
        left: -5rem;
        width: 9.375rem;
        height: 9.375rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        z-index: 1;
        animation: floating calc(var(--animation-slow) * 1.5) infinite ease-in-out reverse;
    }

    @media (min-width: 576px) {
        .course-header::after {
            width: 12.5rem;
            height: 12.5rem;
        }
    }

    @media (min-width: 768px) {
        .course-header::after {
            width: 18.75rem;
            height: 18.75rem;
        }
    }

    /* Decorative elements */
    .course-header .decoration-1 {
        position: absolute;
        top: 20%;
        right: 10%;
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.625rem;
        transform: rotate(45deg);
        z-index: 1;
        animation: floating 4s infinite ease-in-out;
        display: none;
    }

    @media (min-width: 576px) {
        .course-header .decoration-1 {
            display: block;
            width: 3.75rem;
            height: 3.75rem;
            border-radius: 0.9375rem;
        }
    }

    @media (min-width: 768px) {
        .course-header .decoration-1 {
            width: 5rem;
            height: 5rem;
            border-radius: 1.25rem;
        }
    }

    .course-header .decoration-2 {
        position: absolute;
        bottom: 15%;
        left: 5%;
        width: 3.75rem;
        height: 3.75rem;
        border: 0.1875rem solid rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 1;
        animation: pulse 6s infinite ease-in-out;
        display: none;
    }

    @media (min-width: 576px) {
        .course-header .decoration-2 {
            display: block;
            width: 5rem;
            height: 5rem;
        }
    }

    @media (min-width: 768px) {
        .course-header .decoration-2 {
            width: 7.5rem;
            height: 7.5rem;
        }
    }

    .course-header-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.9375rem;
        position: relative;
        z-index: 2;
        width: 100%;
    }

    @media (min-width: 576px) {
        .course-header-content {
            gap: 1.25rem;
        }
    }

    @media (min-width: 768px) {
        .course-header-content {
            grid-template-columns: 3fr 2fr;
            gap: 1.875rem;
        }
    }

    /* Course Image */
    .course-image-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 0.9375rem;
    }

    @media (min-width: 576px) {
        .course-image-wrapper {
            margin-top: 0.625rem;
        }
    }

    @media (min-width: 768px) {
        .course-image-wrapper {
            margin-top: 0;
        }
    }

    .course-image-container {
        position: relative;
        overflow: hidden;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        transition: transform var(--animation-medium) ease, box-shadow var(--animation-medium) ease;
        border: 0.1875rem solid var(--white);
        width: 100%;
        max-width: 21.875rem;
    }

    @media (min-width: 576px) {
        .course-image-container {
            border: 0.25rem solid var(--white);
            max-width: 25rem;
        }
    }

    @media (min-width: 768px) {
        .course-image-container {
            border: 0.3125rem solid var(--white);
            max-width: 31.25rem;
        }
    }

    .course-image-container:hover {
        transform: translateY(-0.625rem);
        box-shadow: 0 0.9375rem 1.875rem rgba(0, 0, 0, 0.2);
    }

    .course-image {
        width: 100%;
        height: 11.25rem;
        object-fit: cover;
        transition: transform var(--animation-medium) ease;
        display: block;
    }

    @media (min-width: 576px) {
        .course-image {
            height: 13.75rem;
        }
    }

    @media (min-width: 768px) {
        .course-image {
            height: 15.625rem;
        }
    }

    .course-image-container:hover .course-image {
        transform: scale(1.05);
    }

    .course-image-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 50%);
        z-index: 1;
        opacity: 0.7;
        transition: opacity var(--animation-medium) ease;
    }

    .course-image-container:hover::after {
        opacity: 0.5;
    }

    .course-price-tag {
        position: absolute;
        top: 0.625rem;
        right: 0.625rem;
        background: var(--secondary-color);
        color: var(--primary-dark);
        padding: 0.375rem 0.75rem;
        border-radius: var(--border-radius-xl);
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: var(--shadow-md);
        transition: transform var(--animation-fast) ease, box-shadow var(--animation-fast) ease;
        z-index: 2;
    }

    @media (min-width: 576px) {
        .course-price-tag {
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.4375rem 0.875rem;
            font-size: 0.9375rem;
        }
    }

    @media (min-width: 768px) {
        .course-price-tag {
            top: 0.9375rem;
            right: 0.9375rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
    }

    .course-image-container:hover .course-price-tag {
        transform: scale(1.1) translateY(-0.3125rem);
        box-shadow: 0 0.625rem 1.25rem rgba(0, 0, 0, 0.15);
        background: var(--white);
    }

    .course-info {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 0 0.3125rem;
    }

    @media (min-width: 576px) {
        .course-info {
            padding: 0 0.625rem;
        }
    }

    @media (min-width: 768px) {
        .course-info {
            padding: 0;
        }
    }

    .course-category {
        display: inline-block;
        background: var(--secondary-color);
        color: var(--primary-dark);
        padding: 0.375rem 0.75rem;
        border-radius: var(--border-radius-xl);
        margin-bottom: 0.625rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03125rem;
        transition: all var(--animation-fast) ease;
        box-shadow: 0 0.1875rem 0.625rem rgba(0, 0, 0, 0.1);
    }

    @media (min-width: 576px) {
        .course-category {
            padding: 0.4375rem 0.875rem;
            margin-bottom: 0.75rem;
            font-size: 0.8125rem;
            letter-spacing: 0.05rem;
        }
    }

    @media (min-width: 768px) {
        .course-category {
            padding: 0.5rem 1rem;
            margin-bottom: 0.9375rem;
            font-size: 0.875rem;
            letter-spacing: 0.0625rem;
        }
    }

    .course-category:hover {
        background: var(--white);
        transform: translateY(-0.1875rem) scale(1.05);
        box-shadow: 0 0.3125rem 0.9375rem rgba(0, 0, 0, 0.15);
    }

    .course-title {
        font-size: 1.375rem;
        font-weight: 700;
        margin-bottom: 0.9375rem;
        text-shadow: 0 0.125rem 0.625rem rgba(0, 0, 0, 0.2);
        line-height: 1.3;
        position: relative;
        padding-right: 0.625rem;
        border-right: 0.1875rem solid var(--secondary-color);
        width: 100%;
    }

    @media (min-width: 576px) {
        .course-title {
            font-size: 1.625rem;
            margin-bottom: 1.125rem;
            padding-right: 0.75rem;
            border-right-width: 0.25rem;
        }
    }

    @media (min-width: 768px) {
        .course-title {
            font-size: 2rem;
            margin-bottom: 1.25rem;
            padding-right: 0.9375rem;
        }
    }

    @media (min-width: 992px) {
        .course-title {
            font-size: 2.25rem;
        }
    }

    /* Course Stats */
    .course-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.9375rem;
        width: 100%;
    }

    @media (min-width: 576px) {
        .course-stats {
            gap: 0.625rem;
            margin-bottom: 1.125rem;
        }
    }

    @media (min-width: 768px) {
        .course-stats {
            margin-bottom: 1.25rem;
        }
    }

    .stat-item {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 0.375rem 0.75rem;
        border-radius: var(--border-radius-xl);
        backdrop-filter: blur(5px);
        transition: all var(--animation-fast) ease;
        border: 0.0625rem solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 0.1875rem 0.625rem rgba(0, 0, 0, 0.1);
        flex: 1 1 calc(50% - 0.5rem);
        min-width: 7.5rem;
        justify-content: flex-start;
        font-size: 0.8125rem;
    }

    @media (min-width: 576px) {
        .stat-item {
            padding: 0.4375rem 0.875rem;
            font-size: 0.875rem;
            flex: 1 1 calc(50% - 0.625rem);
            min-width: 8.75rem;
        }
    }

    @media (min-width: 768px) {
        .stat-item {
            padding: 0.5rem 1rem;
            flex: 0 1 auto;
            min-width: 9.375rem;
            font-size: 0.9375rem;
        }
    }

    .stat-item:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-0.1875rem);
        box-shadow: 0 0.3125rem 0.9375rem rgba(0, 0, 0, 0.15);
    }

    .stat-item i {
        margin-right: 0.5rem;
        color: var(--secondary-color);
        font-size: 0.875rem;
        min-width: 0.875rem;
    }

    @media (min-width: 576px) {
        .stat-item i {
            margin-right: 0.625rem;
            font-size: 1rem;
            min-width: 1rem;
        }
    }

    @media (min-width: 768px) {
        .stat-item i {
            font-size: 1.125rem;
            min-width: 1.125rem;
        }
    }

    .instructor-info {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    @media (min-width: 576px) {
        .instructor-info {
            margin-top: 0.625rem;
            font-size: 0.9375rem;
        }
    }

    @media (min-width: 768px) {
        .instructor-info {
            font-size: 1rem;
        }
    }

    /* Main Content Area */
    .main-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.875rem;
        margin-bottom: 1.875rem;
        width: 100%;
    }

    @media (min-width: 992px) {
        .main-content {
            grid-template-columns: 1.7fr 1.3fr;
            margin-bottom: 3.125rem;
        }
    }

    /* Course Sidebar */
    .course-sidebar {
        width: 100%;
        order: -1;
        max-width: 31.25rem;
        margin: 0 auto;
    }

    @media (min-width: 992px) {
        .course-sidebar {
            order: 0;
            max-width: none;
            margin: 0;
        }
    }

    /* Course Main */
    .course-main {
        width: 100%;
    }

    /* Sections */
    .section {
        margin-bottom: 1.875rem;
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        transition: all var(--animation-medium) ease;
        border: 0.0625rem solid rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    @media (min-width: 768px) {
        .section {
            padding: 1.875rem;
            margin-bottom: 2.5rem;
        }
    }

    .section:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-0.3125rem);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        position: relative;
        padding-bottom: 0.9375rem;
        color: var(--primary-color);
        border-bottom: 0.0625rem solid var(--medium-gray);
        width: 100%;
    }

    @media (min-width: 768px) {
        .section-title {
            font-size: 1.75rem;
            margin-bottom: 1.875rem;
        }
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -0.0625rem;
        right: 0;
        width: 3.125rem;
        height: 0.1875rem;
        background: var(--secondary-color);
        transition: width var(--animation-medium) ease;
    }

    .section-title:hover::after {
        width: 6.25rem;
    }

    .section p {
        line-height: 1.8;
        color: var(--text-light);
        font-size: 1rem;
        word-wrap: break-word;
    }

    /* Course Action Container */
    .course-action-container {
        background: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 1.875rem;
        transition: transform var(--animation-medium) ease, box-shadow var(--animation-medium) ease;
        border: 0.0625rem solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    @media (min-width: 768px) {
        .course-action-container {
            padding: 1.875rem;
        }
    }

    @media (min-width: 992px) {
        .course-action-container {
            position: sticky;
            top: 1.25rem;
            width: 100%;
        }
    }

    .course-action-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 0.3125rem;
        background: var(--gradient-secondary);
    }

    .course-action-container:hover {
        transform: translateY(-0.3125rem);
        box-shadow: var(--shadow-lg);
    }

    .course-action-container h3 {
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    @media (min-width: 768px) {
        .course-action-container h3 {
            font-size: 1.75rem;
        }
    }

    .course-action-container h3::before {
        content: '$';
        color: var(--secondary-color);
        margin-right: 0.625rem;
        font-size: 1.5rem;
    }

    /* Feature Items */
    .course-features {
        margin-top: 1.5625rem;
        border-top: 0.0625rem solid var(--medium-gray);
        padding-top: 1.25rem;
        width: 100%;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.625rem;
        padding: 0.625rem;
        border-radius: var(--border-radius-md);
        transition: all var(--animation-fast) ease;
        background-color: var(--light-gray);
        width: 100%;
    }

    .feature-item:hover {
        background: var(--primary-light);
        transform: translateX(0.3125rem);
    }

    .feature-item i {
        color: var(--primary-color);
        margin-right: 0.625rem;
        font-size: 1rem;
        transition: transform var(--animation-fast) ease;
        background-color: var(--white);
        width: 1.875rem;
        height: 1.875rem;
        min-width: 1.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: var(--shadow-sm);
    }

    @media (min-width: 768px) {
        .feature-item i {
            font-size: 1.125rem;
        }
    }

    .feature-item:hover i {
        transform: scale(1.2);
        color: var(--secondary-color);
    }

    .feature-item span {
        font-weight: 500;
        white-space: normal;
        word-break: break-word;
        font-size: 0.9375rem;
    }

    @media (min-width: 768px) {
        .feature-item span {
            font-size: 1rem;
        }
    }

    /* Buttons */
    .btn {
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        text-align: center;
        border: none;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.625rem 1.25rem;
        border-radius: var(--border-radius-md);
        transition: all 0.3s ease;
        font-family: var(--font-heading);
    }

    @media (min-width: 768px) {
        .btn {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
        }
    }

    .btn-primary {
        background: var(--primary-color);
        color: var(--white);
        box-shadow: 0 0.25rem 0.375rem rgba(0, 51, 102, 0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-0.1875rem);
        box-shadow: 0 0.375rem 0.9375rem rgba(0, 51, 102, 0.3);
    }

    .btn-outline {
        background: transparent;
        border: 0.125rem solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline:hover {
        background: var(--primary-color);
        color: var(--white);
        transform: translateY(-0.1875rem);
        box-shadow: 0 0.375rem 0.9375rem rgba(0, 51, 102, 0.2);
    }

    .btn-full {
        width: 100%;
        display: block;
    }

    /* Enrollment Button */
    .enroll-btn {
        background: var(--gradient-secondary);
        color: var(--primary-dark);
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: var(--border-radius-xl);
        font-weight: 700;
        width: 100%;
        transition: all var(--animation-medium) ease;
        font-size: 1rem;
        letter-spacing: 0.03125rem;
        position: relative;
        overflow: hidden;
        z-index: 1;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        box-shadow: 0 0.375rem 0.9375rem rgba(255, 215, 0, 0.3);
        font-family: var(--font-heading);
    }

    @media (min-width: 768px) {
        .enroll-btn {
            padding: 1rem 1.875rem;
            font-size: 1.125rem;
        }
    }

    .enroll-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.7s ease;
        z-index: -1;
    }

    .enroll-btn:hover {
        transform: translateY(-0.3125rem);
        box-shadow: 0 0.625rem 1.5625rem rgba(255, 215, 0, 0.4);
        background: var(--secondary-color);
    }

    .enroll-btn:hover::before {
        left: 100%;
    }

    .enroll-btn:disabled {
        background: var(--medium-gray);
        color: var(--dark-gray);
        cursor: not-allowed;
        box-shadow: none;
    }

    .enroll-btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .enroll-btn i {
        margin-right: 0.625rem;
        font-size: 1rem;
    }

    @media (min-width: 768px) {
        .enroll-btn i {
            font-size: 1.125rem;
        }
    }

    /* Curriculum Accordion */
    .accordion {
        width: 100%;
    }

    .accordion-item {
        border: 1px solid rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        transition: all var(--animation-fast) ease;
        background-color: var(--white);
        width: 100%;
    }

    @media (min-width: 768px) {
        .accordion-item {
            margin-bottom: 15px;
        }
    }

    .accordion-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
        border-color: var(--primary-light);
    }

    .accordion-header {
        background: var(--white);
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .accordion-button {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        font-weight: 600;
        background: var(--white);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: right;
        color: var(--primary-color);
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .accordion-button {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            font-size: 17px;
        }
    }

    .accordion-button::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 5px;
        background-color: var(--secondary-color);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .accordion-button:hover::before {
        opacity: 1;
    }

    .accordion-button:not(.collapsed) {
        background-color: var(--primary-light);
        color: var(--primary-dark);
        font-weight: 700;
    }

    .accordion-button:not(.collapsed)::before {
        opacity: 1;
    }

    .accordion-button span {
        background-color: var(--primary-light);
        color: var(--primary-color);
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 8px;
        align-self: flex-start;
    }

    @media (min-width: 768px) {
        .accordion-button span {
            font-size: 14px;
            margin-top: 0;
            align-self: center;
        }
    }

    .accordion-body {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background-color: var(--white);
        width: 100%;
    }

    .accordion-collapse.show .accordion-body {
        max-height: 1000px; /* Adjust as needed */
        transition: max-height 0.5s ease;
    }

    /* Curriculum Items */
    .curriculum-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 12px 15px;
        border-bottom: 1px solid var(--medium-gray);
        transition: all var(--animation-fast) ease;
        position: relative;
        width: 100%;
    }

    @media (min-width: 768px) {
        .curriculum-item {
            flex-direction: row;
            align-items: center;
            padding: 15px 20px;
        }
    }

    .curriculum-item:last-child {
        border-bottom: none;
    }

    .curriculum-item:hover {
        background-color: var(--light-gray);
    }

    @media (min-width: 768px) {
        .curriculum-item:hover {
            padding-right: 25px;
        }
    }

    .curriculum-item i {
        margin-right: 15px;
        color: var(--secondary-color);
        transition: transform var(--animation-fast) ease;
        font-size: 16px;
        background-color: var(--primary-light);
        width: 28px;
        height: 28px;
        min-width: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 8px;
    }

    @media (min-width: 768px) {
        .curriculum-item i {
            font-size: 18px;
            width: 32px;
            height: 32px;
            min-width: 32px;
            margin-bottom: 0;
        }
    }

    .curriculum-item:hover i {
        transform: scale(1.2);
        color: var(--primary-color);
        background-color: var(--secondary-color);
    }

    .curriculum-item span {
        word-break: break-word;
    }

    .curriculum-item span:last-child {
        margin-top: 8px;
        margin-right: 0;
        background-color: var(--light-gray);
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        color: var(--dark-gray);
        align-self: flex-start;
    }

    @media (min-width: 768px) {
        .curriculum-item span:last-child {
            margin-top: 0;
            margin-right: auto;
            font-size: 12px;
            align-self: center;
        }
    }

    /* Instructor Card */
    .instructor-card {
        background: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 20px;
        margin-bottom: 30px;
        transition: all var(--animation-medium) ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    @media (min-width: 768px) {
        .instructor-card {
            padding: 30px;
            margin-bottom: 40px;
        }
    }

    .instructor-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient-primary);
    }

    .instructor-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-5px);
    }

    .instructor-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .instructor-header {
            flex-direction: row;
            align-items: flex-start;
            text-align: left;
            gap: 20px;
        }
    }

    .instructor-image-container {
        flex-shrink: 0;
    }

    .instructor-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid var(--white);
        box-shadow: var(--shadow-sm);
        transition: all var(--animation-medium) ease;
        position: relative;
    }

    @media (min-width: 768px) {
        .instructor-image {
            width: 120px;
            height: 120px;
        }
    }

    .instructor-image::after {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border-radius: 50%;
        border: 2px solid var(--secondary-color);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .instructor-card:hover .instructor-image {
        box-shadow: var(--shadow-md);
        transform: scale(1.05);
    }

    .instructor-card:hover .instructor-image::after {
        opacity: 1;
    }

    .instructor-details {
        flex-grow: 1;
        width: 100%;
    }

    .instructor-card h4 {
        color: var(--primary-color);
        font-size: 20px;
        margin-bottom: 5px;
    }

    @media (min-width: 768px) {
        .instructor-card h4 {
            font-size: 22px;
        }
    }

    .instructor-card .text-muted {
        color: var(--text-light);
        font-size: 14px;
        margin-bottom: 10px;
        display: block;
    }

    .instructor-stats {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .instructor-stats {
            justify-content: flex-start;
            gap: 15px;
        }
    }

    .instructor-stat-item {
        display: flex;
        align-items: center;
        background-color: var(--light-gray);
        padding: 6px 12px;
        border-radius: var(--border-radius-xl);
        font-size: 13px;
        transition: all var(--animation-fast) ease;
    }

    @media (min-width: 768px) {
        .instructor-stat-item {
            padding: 8px 15px;
            font-size: 14px;
        }
    }

    .instructor-stat-item:hover {
        background-color: var(--primary-light);
        transform: translateY(-3px);
    }

    .instructor-stat-item i {
        color: var(--primary-color);
        margin-right: 8px;
        font-size: 16px;
    }

    .instructor-bio {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--medium-gray);
        color: var(--text-light);
        line-height: 1.7;
    }

    .mt-15 {
        margin-top: 15px;
    }

    /* Rating Stars */
    .star-rating {
        display: flex;
        align-items: center;
    }

    .star-rating i {
        color: var(--secondary-color);
        margin-right: 3px;
        transition: transform var(--animation-fast) ease;
        font-size: 16px;
    }

    .star-rating:hover i {
        animation: pulse var(--animation-fast) ease;
    }

    .star-rating .far.fa-star {
        color: #d1d1d1;
    }

    .star-rating span {
        margin-left: 8px;
        font-size: 14px;
        color: var(--text-light);
    }

    /* Rating Progress Bars */
    .rating-progress {
        flex-grow: 1;
        height: 10px;
        background: var(--light-gray);
        border-radius: 5px;
        margin-right: 10px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .rating-progress-bar {
        height: 100%;
        background: var(--secondary-color);
        transform-origin: left;
        transition: width 1.5s cubic-bezier(0.22, 0.61, 0.36, 1);
        border-radius: 5px;
    }

    .rating-bar {
        margin-bottom: 15px;
    }

    .rating-label {
        font-weight: 600;
        color: var(--primary-color);
        width: 30px;
        text-align: center;
    }

    .rating-count {
        font-weight: 500;
        color: var(--text-light);
        width: 40px;
        text-align: right;
    }

    .reviews-average {
        background-color: var(--white);
        padding: 25px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .reviews-bars {
        background-color: var(--white);
        padding: 25px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Alerts */
    .alert {
        padding: 18px 20px;
        margin-bottom: 25px;
        border-radius: var(--border-radius-md);
        font-weight: 500;
        position: relative;
        border-right: 4px solid transparent;
        box-shadow: var(--shadow-sm);
    }

    .alert-warning {
        background-color: #FFF8E1;
        color: #F57F17;
        border-color: var(--warning-color);
    }

    .alert-info {
        background-color: #E3F2FD;
        color: #0288D1;
        border-color: #0288D1;
    }

    .alert strong {
        font-weight: 700;
        margin-right: 5px;
    }

    /* Reviews */
    .reviews-summary {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
    }

    @media (min-width: 768px) {
        .reviews-summary {
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }
    }

    .reviews-average {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .reviews-average h1 {
        font-size: 36px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    @media (min-width: 768px) {
        .reviews-average h1 {
            font-size: 48px;
        }
    }

    .reviews-average p {
        color: var(--text-light);
        font-size: 14px;
        margin-top: 10px;
    }

    .reviews-bars {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100%;
    }

    /* Review Cards */
    .review-card {
        background: var(--white);
        padding: 20px;
        margin-bottom: 15px;
        border-radius: var(--border-radius-md);
        transition: all var(--animation-medium) ease;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        width: 100%;
    }

    @media (min-width: 768px) {
        .review-card {
            padding: 25px;
            margin-bottom: 20px;
        }
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .review-card::before {
        content: '"';
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 40px;
        color: var(--primary-light);
        font-family: Georgia, serif;
        opacity: 0.3;
        line-height: 1;
    }

    @media (min-width: 768px) {
        .review-card::before {
            font-size: 60px;
        }
    }

    .review-header {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
        border-bottom: 1px solid var(--medium-gray);
        padding-bottom: 15px;
    }

    @media (min-width: 768px) {
        .review-header {
            flex-direction: row;
            justify-content: space-between;
        }
    }

    .reviewer-name {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    @media (min-width: 768px) {
        .reviewer-name {
            margin-bottom: 0;
        }
    }

    .review-date {
        color: var(--text-light);
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .review-date {
            font-size: 14px;
        }
    }

    .review-card p {
        line-height: 1.7;
        color: var(--text-light);
        position: relative;
        z-index: 1;
        word-break: break-word;
    }

    /* Review Form */
    .add-review-form {
        background: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 20px;
        margin-top: 30px;
        transition: all var(--animation-medium) ease;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    @media (min-width: 768px) {
        .add-review-form {
            padding: 30px;
            margin-top: 40px;
        }
    }

    .add-review-form::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient-primary);
    }

    .add-review-form:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-5px);
    }

    .add-review-form h4 {
        color: var(--primary-color);
        font-size: 20px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--medium-gray);
    }

    @media (min-width: 768px) {
        .add-review-form h4 {
            font-size: 22px;
            margin-bottom: 20px;
        }
    }

    .form-group {
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .form-group {
            margin-bottom: 25px;
        }
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--primary-color);
    }

    @media (min-width: 768px) {
        .form-label {
            margin-bottom: 10px;
        }
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--medium-gray);
        font-size: 14px;
        transition: all var(--animation-fast) ease;
        background-color: var(--light-gray);
    }

    @media (min-width: 768px) {
        .form-control {
            padding: 14px;
            font-size: 16px;
        }
    }

    .form-control:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        border-color: var(--primary-color);
        background-color: var(--white);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    @media (min-width: 768px) {
        textarea.form-control {
            min-height: 150px;
        }
    }

    .form-text {
        margin-top: 6px;
        font-size: 12px;
        color: var(--text-light);
        font-style: italic;
    }

    @media (min-width: 768px) {
        .form-text {
            margin-top: 8px;
            font-size: 14px;
        }
    }

    /* Custom Star Rating Input */
    .rating-input {
        margin-bottom: 15px;
    }

    /* Related Courses */
    .related-courses-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        width: 100%;
    }

    @media (min-width: 576px) {
        .related-courses-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .related-courses-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
    }

    .related-course-wrapper {
        height: 100%;
        width: 100%;
    }

    .related-course-card {
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all var(--animation-medium) ease;
        height: 100%;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: var(--white);
        display: flex;
        flex-direction: column;
        position: relative;
        width: 100%;
    }

    .related-course-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .related-course-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient-primary);
        opacity: 0;
        transition: opacity var(--animation-medium) ease;
    }

    .related-course-card:hover::after {
        opacity: 1;
    }

    .related-course-image-container {
        position: relative;
        overflow: hidden;
        height: 150px;
    }

    @media (min-width: 768px) {
        .related-course-image-container {
            height: 180px;
        }
    }

    .related-course-image {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform var(--animation-medium) ease;
    }

    .related-course-card:hover .related-course-image {
        transform: scale(1.1);
    }

    .related-course-price-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--secondary-color);
        color: var(--primary-dark);
        padding: 5px 10px;
        border-radius: var(--border-radius-xl);
        font-weight: 700;
        font-size: 12px;
        box-shadow: var(--shadow-sm);
        z-index: 2;
        transition: all var(--animation-fast) ease;
    }

    @media (min-width: 768px) {
        .related-course-price-tag {
            font-size: 14px;
        }
    }

    .related-course-card:hover .related-course-price-tag {
        background: var(--white);
        transform: scale(1.05);
    }

    .related-course-content {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    @media (min-width: 768px) {
        .related-course-content {
            padding: 20px;
        }
    }

    .related-course-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color var(--animation-fast) ease;
        color: var(--primary-color);
        line-height: 1.4;
        height: auto;
        max-height: 45px;
    }

    @media (min-width: 768px) {
        .related-course-title {
            font-size: 18px;
            margin-bottom: 10px;
            max-height: 50px;
        }
    }

    .related-course-card:hover .related-course-title {
        color: var(--primary-dark);
    }

    .related-course-instructor {
        color: var(--text-light);
        font-size: 12px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    @media (min-width: 768px) {
        .related-course-instructor {
            font-size: 14px;
            margin-bottom: 15px;
        }
    }

    .related-course-instructor i {
        color: var(--primary-color);
        margin-right: 8px;
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .related-course-instructor i {
            font-size: 14px;
        }
    }

    .related-course-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .related-course-stats {
            flex-direction: row;
            justify-content: space-between;
            font-size: 14px;
        }
    }

    .related-course-lessons {
        display: flex;
        align-items: center;
        color: var(--text-light);
    }

    .related-course-lessons i {
        color: var(--primary-color);
        margin-right: 5px;
    }

    .related-course-card .btn {
        margin-top: auto;
        width: 100%;
    }

    /* Custom Star Rating Input */
    .star-rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        width: 100%;
    }

    .star-rating-input input {
        display: none;
    }

    .star-rating-input label {
        cursor: pointer;
        font-size: 24px;
        color: #ddd;
        margin-right: 5px;
        transition: all 0.2s ease;
    }

    @media (min-width: 768px) {
        .star-rating-input label {
            font-size: 30px;
            margin-right: 8px;
        }
    }

    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label,
    .star-rating-input input:checked ~ label {
        color: var(--secondary-color);
        transform: scale(1.2);
    }

    .star-rating-input label i {
        transition: transform 0.2s ease;
    }

    .star-rating-input label:hover i {
        transform: rotate(-15deg);
    }

    /* RTL Adjustments */
    html[dir="rtl"] .course-price-tag {
        right: auto;
        left: 15px;
    }

    @media (min-width: 768px) {
        html[dir="rtl"] .course-price-tag {
            left: 20px;
        }
    }

    html[dir="rtl"] .section-title::after {
        left: auto;
        right: 0;
    }

    html[dir="rtl"] .stat-item i,
    html[dir="rtl"] .feature-item i,
    html[dir="rtl"] .curriculum-item i,
    html[dir="rtl"] .related-course-instructor i,
    html[dir="rtl"] .related-course-lessons i,
    html[dir="rtl"] .enroll-btn i {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .course-title {
        padding-right: 0;
        padding-left: 15px;
        border-right: none;
        border-left: 4px solid var(--secondary-color);
    }

    html[dir="rtl"] .accordion-button {
        text-align: left;
    }

    html[dir="rtl"] .review-card::before {
        right: auto;
        left: 20px;
    }

    html[dir="rtl"] .feature-item:hover {
        transform: translateX(-5px);
    }

    html[dir="rtl"] .course-title {
        padding-right: 0;
        padding-left: 15px;
        border-right: none;
        border-left: 4px solid var(--secondary-color);
    }

    html[dir="rtl"] .curriculum-item:hover {
        padding-right: 20px;
        padding-left: 25px;
    }

    html[dir="rtl"] .accordion-button {
        text-align: left;
    }

    html[dir="rtl"] .accordion-button::before {
        right: auto;
        left: 0;
    }

    html[dir="rtl"] .enroll-btn i,
    html[dir="rtl"] .btn i {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .instructor-image {
        margin-right: 0;
        margin-left: 20px;
    }

    html[dir="rtl"] .star-rating i {
        margin-right: 0;
        margin-left: 3px;
    }

    html[dir="rtl"] .star-rating span {
        margin-left: 0;
        margin-right: 8px;
    }

    html[dir="rtl"] .rating-progress {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .rating-label {
        text-align: right;
    }

    html[dir="rtl"] .rating-count {
        text-align: left;
    }

    /* Responsive Styles */
    /* Large Desktops */
    @media (max-width: 1200px) {
        .container {
            width: 95%;
            max-width: 1140px;
        }
    }

    /* Tablets and Small Desktops */
    @media (max-width: 991px) {
        /* Layout */
        .course-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .main-content {
            grid-template-columns: 1fr;
        }

        /* Sidebar */
        .course-sidebar {
            order: -1;
        }

        .course-action-container {
            position: relative;
            top: 0;
            margin: 0 auto 30px;
            max-width: 500px;
        }

        /* Course Image */
        .course-image-container {
            margin: 20px auto 0;
        }

        /* Related Courses */
        .related-courses-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Reviews */
        .reviews-summary {
            grid-template-columns: 1fr;
        }
    }

    /* Tablets and Large Phones */
    @media (max-width: 768px) {
        /* Course Header */
        .course-stats {
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1 1 calc(50% - 10px);
            justify-content: flex-start;
        }

        /* Related Courses */
        .related-courses-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        /* Instructor Card */
        .instructor-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .instructor-stats {
            justify-content: center;
        }

        .instructor-bio {
            text-align: center;
        }

        /* Reviews */
        .star-rating {
            justify-content: center;
        }
    }

    /* Mobile Phones */
    @media (max-width: 576px) {
        /* Container */
        .container {
            width: 100%;
            padding: 0 15px;
        }

        /* Course Header */
        .course-title {
            font-size: 24px;
            line-height: 1.4;
        }

        /* Course Stats */
        .stat-item {
            flex: 1 1 100%;
            width: 100%;
        }

        /* Sections */
        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
        }

        /* Accordion */
        .accordion-button {
            font-size: 16px;
            padding: 15px;
        }

        .accordion-button span {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 5px;
        }

        /* Curriculum */
        .curriculum-item {
            padding: 12px 15px;
        }

        /* Features */
        .feature-item {
            padding: 8px;
            width: 100%;
        }

        .feature-item i {
            width: 25px;
            height: 25px;
            font-size: 14px;
        }

        /* Buttons */
        .enroll-btn {
            font-size: 16px;
            padding: 12px 20px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            width: 100%;
        }

        /* Course Action Container */
        .course-action-container {
            width: 100%;
            max-width: none;
        }

        /* Decorative Elements */
        .decoration-1,
        .decoration-2 {
            display: none;
        }
    }

    /* Small Mobile Phones */
    @media (max-width: 480px) {
        /* Course Header */
        .course-title {
            font-size: 22px;
        }

        /* Sections */
        .section {
            padding: 15px;
        }

        .section-title {
            font-size: 20px;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        /* Reviews */
        .review-card {
            padding: 15px;
        }

        .review-header {
            flex-direction: column;
            gap: 5px;
        }

        .review-date {
            font-size: 12px;
        }

        .rating-label, .rating-count {
            font-size: 12px;
        }
    }

    /* Mobile devices in landscape orientation */
    @media (max-height: 500px) and (orientation: landscape) {
        /* Course Header */
        .course-header {
            padding: 30px 0 20px;
        }

        .course-header-content {
            grid-template-columns: 1fr 1fr;
        }

        .course-image-container {
            margin-top: 0;
        }

        .course-title {
            font-size: 22px;
        }

        /* Course Stats */
        .course-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        /* Instructor */
        .instructor-header {
            flex-direction: row;
            align-items: flex-start;
        }

        .instructor-image {
            width: 80px;
            height: 80px;
        }

        /* Reviews */
        .reviews-summary {
            grid-template-columns: 1fr 2fr;
        }

        /* Related Courses */
        .related-courses-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Sections */
        .section {
            padding: 15px;
        }
    }

    /* Fix for RTL layout on mobile */
    @media (max-width: 768px) {
        html[dir="rtl"] .course-title {
            border-right: none;
            border-left: 4px solid var(--secondary-color);
            padding-right: 0;
            padding-left: 15px;
        }

        html[dir="rtl"] .stat-item i {
            margin-right: 0;
            margin-left: 10px;
        }

        html[dir="rtl"] .instructor-stats {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
    <!-- Course Header -->
    <div class="course-header">
        <!-- Decorative elements -->
        <div class="decoration-1"></div>
        <div class="decoration-2"></div>

        <div class="container">
            @if($course->approval_status != 'approved')
            <div class="alert alert-warning mb-20 animated fade-in">
                <strong>ملاحظة:</strong> هذه الدورة في انتظار الموافقة من الإدارة وقد لا تكون متاحة للتسجيل حالياً.
            </div>
            @endif
            <div class="course-header-content">
                <div class="course-info" style="position: relative; z-index: 2;">
                    <div class="course-category animated slide-in-left">{{ $course->category->name ?? 'Uncategorized' }}</div>
                    <h1 class="course-title animated slide-in-left delay-100">{{ $course->title }}</h1>
                    <div class="course-stats animated slide-in-left delay-200">
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <span>{{ number_format($averageRating, 1) }} ({{ $totalRatings }} {{ app()->getLocale() == 'ar' ? 'تقييم' : 'reviews' }})</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->students->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'طالب' : 'students' }}</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $course->videos->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'درس' : 'lessons' }}</span>
                        </div>
                        @if(isset($course->level))
                        <div class="stat-item">
                            <i class="fas fa-signal"></i>
                            <span>
                                @if($course->level == 'beginner')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى مبتدئ' : 'Beginner Level' }}
                                @elseif($course->level == 'intermediate')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متوسط' : 'Intermediate Level' }}
                                @elseif($course->level == 'advanced')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متقدم' : 'Advanced Level' }}
                                @else
                                    {{ $course->level }}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    <p class="instructor-info animated slide-in-left delay-300">
                        {{ app()->getLocale() == 'ar' ? 'أنشئت بواسطة' : 'Created by' }}
                        <strong>{{ $course->instructor->name }}</strong>
                    </p>
                </div>
                <div class="course-image-wrapper" style="position: relative; z-index: 2;">
                    <div class="course-image-container animated slide-in-right delay-100">
                        @if(isset($course->thumbnail) && !empty($course->thumbnail))
                            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}" class="course-image">
                        @else
                            <img src="https://img.freepik.com/free-photo/education-day-arrangement-table-with-copy-space_23-2149068021.jpg" alt="{{ $course->title }}" class="course-image">
                        @endif
                        <div class="course-price-tag">{{ $course->price == 0 ? (app()->getLocale() == 'ar' ? 'مجاني' : 'Free') : '$' . $course->price }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-50">
        <div class="course-grid">
            <!-- Sidebar - Will be shown first on mobile -->
            <div class="course-sidebar">
                <div class="course-action-container animated slide-in-right">
                    <h3 class="mb-20">${{ $course->price }}</h3>

                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-video"></i>
                            <span>{{ $course->videos->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'درس فيديو' : 'Video Lessons' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            @php
                                $hours = 0;
                                if(isset($course->duration)) {
                                    $hours = $course->duration;
                                } elseif(isset($course->videos) && $course->videos->count() > 0) {
                                    $hours = ceil($course->videos->sum('duration_seconds') / 3600);
                                }
                            @endphp
                            <span>{{ $hours }} {{ app()->getLocale() == 'ar' ? 'ساعة من المحتوى' : 'Hours of Content' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $course->materials->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'مورد قابل للتنزيل' : 'Downloadable Resources' }}</span>
                        </div>
                        @if(isset($course->level))
                        <div class="feature-item">
                            <i class="fas fa-signal"></i>
                            <span>
                                @if($course->level == 'beginner')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى مبتدئ' : 'Beginner Level' }}
                                @elseif($course->level == 'intermediate')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متوسط' : 'Intermediate Level' }}
                                @elseif($course->level == 'advanced')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متقدم' : 'Advanced Level' }}
                                @else
                                    {{ $course->level }}
                                @endif
                            </span>
                        </div>
                        @endif
                        @if(isset($course->language))
                        <div class="feature-item">
                            <i class="fas fa-language"></i>
                            <span>
                                @if($course->language == 'en')
                                    {{ app()->getLocale() == 'ar' ? 'اللغة الإنجليزية' : 'English' }}
                                @elseif($course->language == 'ar')
                                    {{ app()->getLocale() == 'ar' ? 'اللغة العربية' : 'Arabic' }}
                                @else
                                    {{ $course->language }}
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="feature-item">
                            <i class="fas fa-medal"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'شهادة إتمام' : 'Certificate of Completion' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-infinity"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'وصول كامل مدى الحياة' : 'Full Lifetime Access' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'الوصول على الجوال والتلفزيون' : 'Access on Mobile and TV' }}</span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('student') || auth()->user()->hasRole('parent'))
                            @php
                                $isEnrolled = App\Models\Enrollment::where('student_id', auth()->user()->user_id)
                                    ->where('course_id', $course->course_id)
                                    ->exists();
                            @endphp

                            @if($isEnrolled)
                                <a href="{{ route('student.course-content', $course->course_id) }}" class="enroll-btn mb-20">
                                    <i class="fas fa-play-circle"></i> {{ app()->getLocale() == 'ar' ? 'متابعة التعلم' : 'Continue Learning' }}
                                </a>
                            @elseif($course->approval_status == 'approved')
                                <form action="{{ route('student.enroll', $course->course_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="enroll-btn mb-20">
                                        <i class="fas fa-graduation-cap"></i> {{ app()->getLocale() == 'ar' ? 'التسجيل في هذه الدورة' : 'Enroll in this Course' }}
                                    </button>
                                </form>
                            @else
                                <button type="button" class="enroll-btn mb-20" disabled>
                                    <i class="fas fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'قيد المراجعة' : 'Pending Approval' }}
                                </button>
                            @endif
                        @elseif(auth()->user()->hasRole('instructor'))
                            <div class="alert alert-info">
                                {{ app()->getLocale() == 'ar' ? 'أنت مسجل الدخول كمدرب ولا يمكنك التسجيل في الدورات.' : 'You are logged in as an instructor and cannot enroll in courses.' }}
                            </div>
                        @endif
                    @else
                        @if($course->approval_status == 'approved')
                        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="enroll-btn mb-20">
                            <i class="fas fa-lock"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل الدخول للتسجيل' : 'Login to Enroll' }}
                        </a>
                        @else
                            <button type="button" class="enroll-btn mb-20" disabled>
                                <i class="fas fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'قيد المراجعة' : 'Pending Approval' }}
                            </button>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Main Content -->
            <div class="course-main">
                <!-- Course Description -->
                <div class="section course-description animated fade-in">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'نبذة عن هذه الدورة' : 'About This Course' }}</h2>
                    <p>{{ $course->description }}</p>
                </div>

                <!-- Course Curriculum -->
                <div class="section course-curriculum animated fade-in delay-100">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'منهج الدورة' : 'Course Curriculum' }}</h2>

                    <div class="accordion" id="curriculumAccordion">
                        @if(isset($course->videos) && $course->videos->count() > 0)
                            @php
                                // Check if videos have sections
                                $hasSection = $course->videos->first() && isset($course->videos->first()->section);

                                if ($hasSection) {
                                    // Group videos by their sections
                                    $sections = $course->videos->groupBy('section');
                                } else {
                                    // Create default sections based on video count
                                    $totalVideos = $course->videos->count();
                                    $sectionsCount = min(3, ceil($totalVideos / 3));
                                    $videosPerSection = ceil($totalVideos / $sectionsCount);

                                    $sections = collect();
                                    $sectionTitles = [
                                        app()->getLocale() == 'ar' ? 'مقدمة' : 'Introduction',
                                        app()->getLocale() == 'ar' ? 'البداية' : 'Getting Started',
                                        app()->getLocale() == 'ar' ? 'موضوعات متقدمة' : 'Advanced Topics'
                                    ];

                                    for ($i = 0; $i < $sectionsCount; $i++) {
                                        $start = $i * $videosPerSection;
                                        $sectionVideos = $course->videos->slice($start, $videosPerSection);
                                        if ($sectionVideos->count() > 0) {
                                            $sections->put($sectionTitles[$i], $sectionVideos);
                                        }
                                    }
                                }
                            @endphp

                            @foreach($sections as $sectionTitle => $sectionVideos)
                                @if(count($sectionVideos) > 0)
                                    <div class="accordion-item animated fade-in" style="animation-delay: {{ $loop->index * 0.1 + 0.2 }}s">
                                        <h2 class="accordion-header" id="heading{{ Str::slug($sectionTitle) }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($sectionTitle) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($sectionTitle) }}">
                                                {{ $sectionTitle }} <span style="margin-left: auto">{{ count($sectionVideos) }} {{ app()->getLocale() == 'ar' ? 'محاضرة' : 'lectures' }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($sectionTitle) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($sectionTitle) }}">
                                            <div class="accordion-body">
                                                @foreach($sectionVideos as $video)
                                                    <div class="curriculum-item">
                                                        <i class="fas fa-play-circle"></i>
                                                        <span>{{ $video->title }}</span>
                                                        <span style="margin-left: auto">
                                                            @if(isset($video->duration_seconds))
                                                                {{ gmdate("i:s", $video->duration_seconds) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="alert alert-info animated fade-in">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد فيديوهات متاحة لهذه الدورة حاليًا.' : 'No videos available for this course yet.' }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="section instructor-info animated fade-in delay-200">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'المدرب' : 'Instructor' }}</h2>

                    <div class="instructor-card">
                        <div class="instructor-header">
                            <div class="instructor-image-container">
                                @if(isset($course->instructor->profile_image) && !empty($course->instructor->profile_image))
                                    <img src="{{ asset($course->instructor->profile_image) }}" alt="{{ $course->instructor->name }}" class="instructor-image">
                                @else
                                    <img src="https://img.freepik.com/free-photo/confident-teacher-with-students-background_23-2148201042.jpg" alt="{{ $course->instructor->name }}" class="instructor-image">
                                @endif
                            </div>
                            <div class="instructor-details">
                                <h4>{{ $course->instructor->name }}</h4>
                                <p class="text-muted">{{ $course->category->name ?? 'Instructor' }} {{ app()->getLocale() == 'ar' ? 'خبير' : 'Expert' }}</p>

                                @php
                                    $instructorRating = 0;
                                    $instructorStudentsCount = 0;
                                    $instructorCoursesCount = 0;

                                    if(isset($course->instructor->ratings)) {
                                        $instructorRating = $course->instructor->ratings->avg('rating_value') ?? 0;
                                    }

                                    if(isset($course->instructor->courses)) {
                                        $instructorCoursesCount = $course->instructor->courses->count();
                                        $instructorStudentsCount = $course->instructor->courses->sum(function($course) {
                                            return $course->students->count() ?? 0;
                                        });
                                    }

                                    $instructorRating = number_format($instructorRating, 1);
                                @endphp

                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($instructorRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $instructorRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span>{{ $instructorRating }} {{ app()->getLocale() == 'ar' ? 'تقييم المدرب' : 'Instructor Rating' }}</span>
                                </div>

                                <div class="instructor-stats">
                                    <div class="instructor-stat-item">
                                        <i class="fas fa-user-graduate"></i>
                                        <span>{{ $instructorStudentsCount }} {{ app()->getLocale() == 'ar' ? 'طالب' : 'Students' }}</span>
                                    </div>
                                    <div class="instructor-stat-item">
                                        <i class="fas fa-book"></i>
                                        <span>{{ $instructorCoursesCount }} {{ app()->getLocale() == 'ar' ? 'دورة' : 'Courses' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="instructor-bio">
                            @if(isset($course->instructor->bio) && !empty($course->instructor->bio))
                                <p>{{ $course->instructor->bio }}</p>
                            @else
                                <p>{{ app()->getLocale() == 'ar' ? 'مدرب متخصص في مجال ' . ($course->category->name ?? '') . ' مع خبرة واسعة في التدريس والتطوير.' : 'A specialized instructor in the field of ' . ($course->category->name ?? '') . ' with extensive experience in teaching and development.' }}</p>
                            @endif
                        </div>
                        <a href="{{ route('instructor.profile', $course->instructor->id ?? $course->instructor->instructor_id ?? '') }}" class="btn btn-outline mt-15">
                            <i class="fas fa-user-tie"></i> {{ app()->getLocale() == 'ar' ? 'عرض الملف الشخصي للمدرب' : 'View Instructor Profile' }}
                        </a>
                    </div>
                </div>

                <!-- Student Reviews -->
                <div class="section reviews animated fade-in delay-300">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'تقييمات الطلاب' : 'Student Reviews' }}</h2>

                    <!-- Rating Summary -->
                    <div class="reviews-summary">
                        <div class="reviews-average text-center animated fade-in delay-400">
                            @php
                                $courseRatings = $course->ratings ?? $course->reviews ?? collect([]);
                                $avgRating = 0;
                                $totalRatings = 0;
                                $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

                                if ($courseRatings->count() > 0) {
                                    $totalRatings = $courseRatings->count();
                                    $avgRating = $courseRatings->avg('rating') ?? $courseRatings->avg('rating_value') ?? 0;

                                    // Count ratings by value
                                    foreach ($courseRatings as $rating) {
                                        $ratingValue = isset($rating->rating) ? $rating->rating : (isset($rating->rating_value) ? $rating->rating_value : 0);
                                        $ratingValue = min(5, max(1, round($ratingValue))); // Ensure it's between 1-5
                                        $ratingCounts[$ratingValue]++;
                                    }
                                }

                                $avgRating = number_format($avgRating, 1);
                                $fullStars = floor($avgRating);
                                $halfStar = ($avgRating - $fullStars) >= 0.5;
                            @endphp

                            <h1 style="font-size: 48px; font-weight: bold; color: var(--primary-color);">{{ $avgRating }}</h1>
                            <div class="star-rating mb-10">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star"></i>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p>{{ $totalRatings }} {{ app()->getLocale() == 'ar' ? 'تقييم' : 'ratings' }}</p>
                        </div>
                        <div class="reviews-bars animated fade-in delay-500">
                            @foreach(range(5, 1) as $rating)
                                <div class="rating-bar" data-rating="{{ $rating }}">
                                    <div class="rating-label">{{ $rating }}</div>
                                    <div class="rating-progress">
                                        @php
                                            $percentage = $totalRatings > 0 ? ($ratingCounts[$rating] / $totalRatings) * 100 : 0;
                                        @endphp
                                        <div class="rating-progress-bar" style="width: 0%" data-width="{{ $percentage }}%"></div>
                                    </div>
                                    <div class="rating-count">{{ $ratingCounts[$rating] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Review List -->
                    <div class="reviews-list">
                        @php
                            // Use reviews from controller if available, otherwise fallback to course relationship
                            $courseRatings = isset($reviews) && $reviews->count() > 0 ? $reviews : ($course->ratings ?? $course->reviews ?? collect([]));
                        @endphp

                        @if($courseRatings->count() > 0)
                            @foreach($courseRatings->take(5) as $index => $rating)
                                <div class="review-card animated fade-in" style="animation-delay: {{ 0.1 * $index + 0.6 }}s">
                                    <div class="review-header">
                                        <span class="reviewer-name">
                                            @if(isset($rating->student) && isset($rating->student->name))
                                                {{ $rating->student->name }}
                                            @elseif(isset($rating->user) && isset($rating->user->name))
                                                {{ $rating->user->name }}
                                            @else
                                                {{ app()->getLocale() == 'ar' ? 'طالب' : 'Student' }}
                                            @endif
                                        </span>
                                        <span class="review-date">{{ isset($rating->created_at) ? $rating->created_at->format('M d, Y') : 'Unknown date' }}</span>
                                    </div>
                                    <div class="star-rating mb-10">
                                        @php
                                            $ratingValue = isset($rating->rating) ? $rating->rating : (isset($rating->rating_value) ? $rating->rating_value : 0);
                                            $ratingValue = min(5, max(1, round($ratingValue))); // Ensure it's between 1-5
                                        @endphp

                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $ratingValue)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p>{{ $rating->comment ?? $rating->review_text ?? $rating->review ?? (app()->getLocale() == 'ar' ? 'دورة رائعة! مفيدة جدا ومنظمة بشكل جيد.' : 'Great course! Very informative and well-structured.') }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info animated fade-in delay-400">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد مراجعات حتى الآن. كن أول من يراجع هذه الدورة!' : 'No reviews yet. Be the first to review this course!' }}
                            </div>
                        @endif

                        <!-- Add Review Form -->
                        @auth
                            @php
                                $isEnrolled = App\Models\Enrollment::where('student_id', auth()->user()->user_id)
                                    ->where('course_id', $course->course_id)
                                    ->exists();

                                $hasReviewed = false;
                                if ($courseReviewsTableExists ?? false) {
                                    $hasReviewed = App\Models\CourseReview::where('user_id', auth()->user()->user_id)
                                        ->where('course_id', $course->course_id)
                                        ->exists();
                                } elseif ($ratingsTableExists ?? false) {
                                    $hasReviewed = App\Models\Rating::where(function($query) {
                                            $query->where('user_id', auth()->user()->user_id)
                                                ->orWhere('student_id', auth()->user()->user_id);
                                        })
                                        ->where('course_id', $course->course_id)
                                        ->exists();
                                }
                            @endphp

                            @if($isEnrolled)
                                <div class="add-review-form mt-50 animated fade-in delay-500">
                                    <h4>{{ app()->getLocale() == 'ar' ? 'أضف تقييمك' : 'Add Your Review' }}</h4>
                                    <form action="{{ route('student.review', $course->course_id) }}" method="POST" class="mt-20">
                                        @csrf
                                        <div class="form-group">
                                            <label for="rating" class="form-label">{{ app()->getLocale() == 'ar' ? 'التقييم' : 'Rating' }}</label>
                                            <div class="rating-input">
                                                <div class="star-rating-input">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }} />
                                                        <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="comment" class="form-label">{{ app()->getLocale() == 'ar' ? 'تعليقك' : 'Your Review' }}</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="4" required minlength="10"></textarea>
                                            <div class="form-text">{{ app()->getLocale() == 'ar' ? 'شارك تجربتك مع هذه الدورة. الحد الأدنى 10 أحرف.' : 'Share your experience with this course. Minimum 10 characters.' }}</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ app()->getLocale() == 'ar' ? 'إرسال التقييم' : 'Submit Review' }}</button>
                                    </form>
                                </div>
                            @elseif(!$hasReviewed)
                                <div class="alert alert-info mt-30 animated fade-in delay-500">
                                    {{ app()->getLocale() == 'ar' ? 'يجب أن تكون مسجلاً في هذه الدورة لإضافة تقييم.' : 'You must be enrolled in this course to add a review.' }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mt-30 animated fade-in delay-500">
                                <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="alert-link">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</a> {{ app()->getLocale() == 'ar' ? 'لإضافة تقييم.' : 'to add a review.' }}
                            </div>
                        @endauth
                    </div>
                </div>
            </div>


        </div>

        <!-- Related Courses -->
        @if(isset($relatedCourses) && count($relatedCourses) > 0)
            <div class="section related-courses mt-50 animated fade-in delay-600">
                <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'دورات ذات صلة' : 'Related Courses' }}</h2>

                <div class="related-courses-grid">
                    @foreach($relatedCourses as $index => $relatedCourse)
                        <div class="related-course-wrapper">
                            <div class="related-course-card animated fade-in" style="animation-delay: {{ 0.1 * $index + 0.7 }}s">
                                <div class="related-course-image-container">
                                    @if(isset($relatedCourse->thumbnail) && !empty($relatedCourse->thumbnail))
                                        <img src="{{ asset($relatedCourse->thumbnail) }}" alt="{{ $relatedCourse->title }}" class="related-course-image">
                                    @else
                                        <img src="https://img.freepik.com/free-photo/student-success-education-lifestyle-concept_23-2148766904.jpg?t=st=1710008242~exp=1710008842~hmac=83a3ad0a86d9b6e6a4c0ef8d61ae46b58e9b9d5b6c08b641a0ad657543b7c0b7" alt="{{ $relatedCourse->title }}" class="related-course-image">
                                    @endif
                                    <div class="related-course-price-tag">${{ $relatedCourse->price == 0 ? (app()->getLocale() == 'ar' ? 'مجاني' : 'Free') : $relatedCourse->price }}</div>
                                </div>
                                <div class="related-course-content">
                                    <h5 class="related-course-title">{{ $relatedCourse->title }}</h5>
                                    <p class="related-course-instructor">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        @if(isset($relatedCourse->instructor) && isset($relatedCourse->instructor->name))
                                            {{ $relatedCourse->instructor->name }}
                                        @elseif(isset($relatedCourse->instructor_name))
                                            {{ $relatedCourse->instructor_name }}
                                        @else
                                            {{ app()->getLocale() == 'ar' ? 'مدرب متميز' : 'Expert Instructor' }}
                                        @endif
                                    </p>
                                    <div class="related-course-stats">
                                        @php
                                            $relatedRatings = $relatedCourse->ratings ?? $relatedCourse->reviews ?? collect([]);
                                            $relatedAvgRating = 0;

                                            if ($relatedRatings->count() > 0) {
                                                $relatedAvgRating = $relatedRatings->avg('rating') ?? $relatedRatings->avg('rating_value') ?? 0;
                                            }

                                            $relatedAvgRating = number_format($relatedAvgRating, 1);
                                            $relatedFullStars = floor($relatedAvgRating);
                                            $relatedHalfStar = ($relatedAvgRating - $relatedFullStars) >= 0.5;
                                        @endphp

                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $relatedFullStars)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i == $relatedFullStars + 1 && $relatedHalfStar)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                            <span>({{ $relatedRatings->count() }})</span>
                                        </div>

                                        <div class="related-course-lessons">
                                            <i class="fas fa-video"></i>
                                            <span>{{ $relatedCourse->videos->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'درس' : 'lessons' }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('course.detail', $relatedCourse->course_id ?? $relatedCourse->id ?? '') }}" class="btn btn-primary btn-full mt-10">{{ app()->getLocale() == 'ar' ? 'عرض الدورة' : 'View Course' }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize accordion functionality
        const accordionButtons = document.querySelectorAll('.accordion-button');

        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get the target collapse
                const targetId = this.getAttribute('data-bs-target').substring(1);
                const targetCollapse = document.getElementById(targetId);

                // Toggle collapsed class
                this.classList.toggle('collapsed');

                // Toggle show class on collapse
                targetCollapse.classList.toggle('show');

                // Update aria-expanded
                const isExpanded = targetCollapse.classList.contains('show');
                this.setAttribute('aria-expanded', isExpanded);
            });
        });

        // Variables para animaciones
        const animatedElements = document.querySelectorAll('.animated');
        const ratingBars = document.querySelectorAll('.rating-progress-bar');
        const accordionItems = document.querySelectorAll('.accordion-item');
        const featureItems = document.querySelectorAll('.feature-item');
        const courseImageContainer = document.querySelector('.course-image-container');

        // Función para animar elementos cuando son visibles
        function animateOnScroll() {
            animatedElements.forEach(element => {
                if (isElementInViewport(element) && !element.classList.contains('animated-visible')) {
                    element.classList.add('animated-visible');
                }
            });

            // Animar barras de progreso cuando son visibles
            ratingBars.forEach(bar => {
                if (isElementInViewport(bar) && !bar.classList.contains('bar-animated')) {
                    const targetWidth = bar.getAttribute('data-width');
                    bar.style.transition = 'width 1s ease-in-out';
                    bar.style.width = targetWidth;
                    bar.classList.add('bar-animated');
                }
            });
        }

        // Comprobar si un elemento está en el viewport
        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.bottom >= 0 &&
                rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
                rect.right >= 0
            );
        }

        // Añadir efectos hover a elementos del curriculum
        accordionItems.forEach(item => {
            item.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = 'var(--shadow-sm)';
            });

            item.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Efecto de pulso en la imagen del curso
        if (courseImageContainer) {
            courseImageContainer.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.2)';
            });

            courseImageContainer.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'var(--shadow-lg)';
            });
        }

        // Iniciar las animaciones
        animateOnScroll();

        // Evento de scroll para animar elementos
        window.addEventListener('scroll', animateOnScroll);

        // Asegurar que las barras de progreso se animen inmediatamente si están visibles
        setTimeout(function() {
            ratingBars.forEach(bar => {
                if (isElementInViewport(bar) && !bar.classList.contains('bar-animated')) {
                    const targetWidth = bar.getAttribute('data-width');
                    bar.style.transition = 'width 1s ease-in-out';
                    bar.style.width = targetWidth;
                    bar.classList.add('bar-animated');
                }
            });
        }, 500);

        // RTL Support
        if (document.dir === 'rtl' || document.documentElement.lang === 'ar') {
            document.documentElement.setAttribute('dir', 'rtl');

            // Adjust any specific RTL styles programmatically if needed
            const featureItems = document.querySelectorAll('.feature-item');
            featureItems.forEach(item => {
                item.addEventListener('mouseover', function() {
                    this.style.transform = 'translateX(-5px)';
                });

                item.addEventListener('mouseout', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        }
    });
</script>
@endsection