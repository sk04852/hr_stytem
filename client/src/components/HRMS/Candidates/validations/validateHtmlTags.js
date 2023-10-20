// Messages
let ErrorText =
  "HTML tagid ei ole lubatud. Palun kustuta kõik tagid väljadelt."; //Html Tags are not allowed. Remove any kind of Html tags from fields

// Reg Expression
let reg = /<(.|\n)*?>/g;

// Validation Funcations
export const validateCandidatePersonalDataHtmlTags = (
  values,
  tags,
  setFormErrors
) => {
  let error = {};

  for (const key in values) {
    if (Object.hasOwnProperty.call(values, key)) {
      const element = values[key];
      if (reg.test(element) || reg.test(tags)) {
        error = { ErrorText };
      }
      setFormErrors(error);
    }
  }

  return error;
};

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
