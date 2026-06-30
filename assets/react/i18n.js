import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

export function initI18n(locale) {
    return i18n
        .use(initReactI18next)
        .init({
            lng: locale,
            fallbackLng: 'en',
            resources: {
                pl: {
                    translation: {
                        choose: 'Wybierz',
                        choose_driver: 'Wybierz kierowcę',
                        hide_drivers: 'Ukryj kierowców',
                    },
                },
                en: {
                    translation: {
                        choose: 'Choose',
                        choose_driver: 'Choose a driver',
                        hide_drivers: 'Hide drivers',
                    },
                },
            },
            interpolation: {
                escapeValue: false,
            },
        });
}
