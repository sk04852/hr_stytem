import i18n from "../../../Shared/i18n/i18n";

// Messages
const ErrorText = i18n.t("errors.tagsNotAllowed");

// Reg Expression
let reg = /<(.|\n)*?>/g;

// Validation Funcations
export const validateHtmlTags = (values, setFormErrors) => {
  let error = {};

  for (const key in values) {
    if (Object.hasOwnProperty.call(values, key)) {
      const element = values[key];
      if (reg.test(element)) {
        error = { ErrorText };
      }
      setFormErrors(error);
    }
  }

  return error;
};
