from pathlib import Path

p = Path(r'C:\xampp\htdocs\p_edunote\public\assets\css\app.css')
text = p.read_text(encoding='utf-8')

start = text.find('/* === SIDEBAR COLLAPSE === */')
end = text.find('/* === RESPONSIVE === */')
if end == -1:
    end = len(text)

new_section = """/* === SIDEBAR COLLAPSE === */
.sidebar {
    width: 260px;
    min-width: 260px;
    background: var(--side-bg);
    color: var(--side-text);
    transition: width 0.3s ease, min-width 0.3s ease;
    overflow: hidden;
}

.sidebar.collapsed {
    width: 60px;
    min-width: 60px;
}

/* Toggle button inside sidebar header */
.sidebar-toggle-btn {
    background: none;
    border: none;
    color: #cfd6ee;
    font-size: 1.3rem;
    cursor: pointer;
    padding: 0 6px;
    line-height: 1;
    margin-left: auto;
    opacity: 0.6;
    transition: opacity 0.15s, transform 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
}

.sidebar-toggle-btn:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.08);
}

/* Collapsed header: logo on top, toggle below */
.sidebar.collapsed .sidebar-brand {
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 0.75rem 0 0.5rem;
    gap: 0.5rem;
}

.sidebar.collapsed .sidebar-brand .logo-icon {
    width: 28px;
    height: 28px;
    font-size: 0.85rem;
}

.sidebar.collapsed .sidebar-toggle-btn {
    margin-left: 0;
    width: 32px;
    height: 32px;
    padding: 0;
    font-size: 1.1rem;
    transform: none;
}

/* Hide text labels when collapsed */
.sidebar.collapsed .brand-text,
.sidebar.collapsed .nav-label,
.sidebar.collapsed .toggle-label,
.sidebar.collapsed .sidebar-section-title,
.sidebar.collapsed .accordion-arrow,
.sidebar.collapsed .sidebar-user {
    display: none;
}

/* Menu links in collapsed rail */
.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.6rem 0;
    border-radius: 0;
}

.sidebar.collapsed .nav-link .feather {
    margin-right: 0;
    width: 18px;
    height: 18px;
}

.sidebar.collapsed .nav-link:hover,
.sidebar.collapsed .nav-link.active {
    background: var(--side-hover);
}

/* Bottom section in collapsed rail */
.sidebar.collapsed .sidebar-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem 0;
    gap: 0.25rem;
}

/* Theme toggle collapsed */
.sidebar.collapsed .theme-toggle {
    justify-content: center;
    padding: 0.55rem 0;
    width: 100%;
    margin-bottom: 0.2rem;
    border-radius: 0;
}

.sidebar.collapsed .theme-toggle:hover {
    background: var(--side-hover);
}

.sidebar.collapsed .theme-toggle-icon {
    font-size: 1.1rem;
    margin: 0;
}

.sidebar.collapsed .theme-toggle-track,
.sidebar.collapsed .theme-toggle-thumb {
    display: none;
}

/* Logout collapsed */
.sidebar.collapsed .sidebar-logout {
    width: 100%;
}

.sidebar.collapsed .sidebar-logout .nav-link {
    justify-content: center;
    padding: 0.6rem 0;
    border-radius: 0;
    background: transparent;
}

.sidebar.collapsed .sidebar-logout .nav-link:hover {
    background: var(--side-hover);
}

.sidebar.collapsed .sidebar-logout .nav-link .feather {
    width: 18px;
    height: 18px;
}

/* Responsive: rail always on mobile */
@media (max-width: 991px) {
    .sidebar {
        width: 60px;
        min-width: 60px;
    }
    .sidebar .sidebar-brand {
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0.75rem 0 0.5rem;
        gap: 0.5rem;
    }
    .sidebar .sidebar-brand .logo-icon {
        width: 28px;
        height: 28px;
        font-size: 0.85rem;
    }
    .sidebar .sidebar-toggle-btn {
        margin-left: 0;
        width: 32px;
        height: 32px;
        padding: 0;
        font-size: 1.1rem;
        transform: none;
    }
    .sidebar .brand-text,
    .sidebar .nav-label,
    .sidebar .toggle-label,
    .sidebar .sidebar-section-title,
    .sidebar .accordion-arrow,
    .sidebar .sidebar-user {
        display: none;
    }
    .sidebar .nav-link {
        justify-content: center;
        padding: 0.6rem 0;
        border-radius: 0;
    }
    .sidebar .nav-link .feather {
        margin-right: 0;
        width: 18px;
        height: 18px;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: var(--side-hover);
    }
    .sidebar .sidebar-bottom {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.5rem 0;
        gap: 0.25rem;
    }
    .sidebar .theme-toggle {
        justify-content: center;
        padding: 0.55rem 0;
        width: 100%;
        margin-bottom: 0.2rem;
        border-radius: 0;
    }
    .sidebar .theme-toggle-icon {
        font-size: 1.1rem;
        margin: 0;
    }
    .sidebar .theme-toggle-track,
    .sidebar .theme-toggle-thumb {
        display: none;
    }
    .sidebar .sidebar-logout {
        width: 100%;
    }
    .sidebar .sidebar-logout .nav-link {
        justify-content: center;
        padding: 0.6rem 0;
        border-radius: 0;
        background: transparent;
    }
    .sidebar .sidebar-logout .nav-link .feather {
        width: 18px;
        height: 18px;
    }
}

"""

new_text = text[:start] + new_section + text[end:]
p.write_text(new_text, encoding='utf-8')
print('CSS sidebar section replaced')
