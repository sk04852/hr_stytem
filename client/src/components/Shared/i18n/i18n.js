import i18n from "i18next";
import { initReactI18next } from "react-i18next";

// import translations
import translationEN from "./locales/en.json";
import translationET from "./locales/et.json";

// the translations
const resources = {
  en: {
    translation: translationEN,
  },
  et: {
    translation: translationET,
  },
};

let selectedLanguage = localStorage.getItem("lang");

i18n
  .use(initReactI18next) // passes i18n down to react-i18next
  .init({
    resources,
    lng: selectedLanguage ? selectedLanguage : 'et',

    // keySeparator: false, // we do not use keys in form messages.welcome

    interpolation: {
      escapeValue: false, // react already safes from xss
    },

    react: {
      wait: true,
    },
  });

export default i18n;
