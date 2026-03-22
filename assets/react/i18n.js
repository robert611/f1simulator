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
                    },
                },
                en: {
                    translation: {
                        choose: 'Choose',
                    },
                },
            },
            interpolation: {
                escapeValue: false,
            },
        });
}
