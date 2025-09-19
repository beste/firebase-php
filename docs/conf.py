# -*- coding: utf-8 -*-

# Project information
project = 'Firebase for PHP'
author = 'Jérôme Gamez'
copyright = 'Jérôme Gamez'
version = '7.x'
release = version  # This will be used by Furo for version display
language = 'en'

# Extensions
extensions = [
    "myst_parser",
    "sphinx_design",
    "sphinx_copybutton",
    "sphinx_inline_tabs",        # Tabbed content blocks
    "sphinxext.opengraph",
    "sphinx_sitemap",
]

# MyST parser configuration
myst_enable_extensions = [
    "colon_fence",
    "deflist",
    "fieldlist",
    "attrs_block",
    "attrs_inline",
    "linkify",
]

# Build configuration
exclude_patterns = ['_build', '.venv']
suppress_warnings = ['image.nonlocal_uri']

# HTML theme and static files
html_title = 'Firebase for PHP'
html_theme = 'furo'
html_static_path = ['_static']
html_css_files = ['custom.css']
html_logo = "_static/logo.svg"

html_theme_options = {
    # GitHub integration
    "source_repository": "https://github.com/kreait/firebase-php/",
    "source_branch": "7.x",
    "source_directory": "docs/",

    # Navigation
    "navigation_with_keys": True,

    # Top page buttons
    "top_of_page_buttons": ["view", "edit"],

    # Announcement banner (uncomment when needed)
    # "announcement": """
    #     🚀 Blazing fast Firebase integration, cosmic documentation experience!
    #     <a href="/overview.html">Explore the stellar API</a> ⭐
    # """,

    "light_css_variables": {
        "color-brand-primary": "#E65100",        # Darker Firebase orange for better contrast
    },
    "dark_css_variables": {
        "color-brand-primary": "#FFB74D",        # Lighter orange for dark mode
    },

    # Footer icons
    "footer_icons": [
        {
            "name": "GitHub",
            "url": "https://github.com/kreait/firebase-php",
            "html": """
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
                </svg>
            """,
        },
    ],
}

# SEO and social media
html_baseurl = "https://firebase-php.readthedocs.io/"
ogp_site_url = "https://firebase-php.readthedocs.io/"
ogp_social_cards = {
    "image": "_static/logo.png",
}
sitemap_url_scheme = "{lang}{version}{link}"

# Extension-specific configuration
copybutton_prompt_text = r">>> |\$ "
copybutton_prompt_is_regexp = True

# Syntax highlighting
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

lexers['php'] = PhpLexer(startinline=True, linenos=1)
lexers['php-annotations'] = PhpLexer(startinline=True, linenos=1)

