const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Quicksand', ...defaultTheme.fontFamily.sans],
                serif: ['Nattyla', ...defaultTheme.fontFamily.serif]
            },
            colors: {
                we4vBg: 'rgba(250, 251, 246)',
                vellum: '#fefde7',
                we4vGreen: {
                    100: '#dcfcec',
                    200: '#c0fade',
                    300: '#91f1c2',
                    400: '#3ee393',
                    500: '#09bd67',
                    600: '#02AC5A',
                    700: '#039c52',
                    800: '#038949',
                    900: '#026033',
                },
                we4vOrange: '#FF9300',
                we4vBlue: '#51A8BA',
                we4vDarkBlue: '#33838E',
                we4vModalBg: 'rgba(0,0,0,0.75)',
                we4vGrey: {
                    100: '#f7f7f4',
                    200: '#e3e2e2',
                    300: '#aba8a8',
                    400: '#939090',
                    500: '#848181',
                    600: '#6e6c6c',
                    700: '#595757',
                    800: '#444242',
                    900: '#292929',
                },
            },
            fontSize: {
                'we4vPost': '0.9rem',
                'we4vComment': '0.8rem',
            },
            width: {
                'we4vCommentPic': '5%',
                'we4vCommentText': '95%',
                'justUnderHalf': '49%',
                'searchTabWidth': '23.5%',
            },
            maxHeight: {
                '600': '600px',
            },
            minHeight: {
                '24': '96px',
            },
            maxWidth: {
                '1/3': '32.5%',
                '1/4': '25%',
                '1/2': '50%',
                '3/4': '75%',
                '5/12': '49%'
            },
            minWidth: {
                '1/3': '32.5%',
                '1/4': '25%',
                '1/5': '20%',
                '1/2': '50%',
                '3/4': '75%',
                '4/12': '31.5%',
                '5/12': '49%',
                '9/12': '75%'
            },
            padding: {
                'indent-0': '0rem',
                'indent-1': '0 0 0 2rem',
                'indent-2': '0 0 0 4rem',
                'indent-3': '0 0 0 6rem',
                'indent-4': '0 0 0 8rem',
                'indent-5': '0 0 0 10rem',
                'indent-6': '0 0 0 12rem',
                'indent-7': '0 0 0 14rem',
                'indent-8': '0 0 0 16rem',
                'indent-9': '0 0 0 18rem',
                'indent-10': '0 0 0 20rem',
            },
            animation: {
                'spin': 'spin 2s linear infinite'
            },
            gridTemplateRows: {
               'groupBox': 'minmax(40px, 40px) auto',
               'projectBox': 'minmax(64px, 64px) auto',
            },
            gridRowEnd: {
                '-1': '-1'
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};