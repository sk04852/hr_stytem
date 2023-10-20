// Messages
const ErrorText =
  "HTML tagid ei ole lubatud. Palun kustuta kõik tagid väljadelt."; //Html Tags are not allowed. Remove any kind of Html tags from fields.
const companyError =
  "Ettevõtet ei leitud. Palun kirjuta kehtiv ettevõtte nimi või proovi hiljem uuesti."; //Company not found. Please provide valid company and or try again later.

// Reg Expression
let reg = /<(.|\n)*?>/g;

// Validation Funcations
export const validateHtmlTags = (values, setFormErrors, location) => {
  let error = {};

  // assign variables
  const companyName = values.en.company_name;
  const personContact = values.en.contacts;
  const companyLocation = values.en.location;
  const companyIndustry =
    location === "EditCompany" ? values.industries : values.industry_names;
  const companyInvoicing = values.invoicing_info;
  const companyVat = values.vat;

  // validate company name
  if (companyName.length === 0) {
    error = { companyError };
  }

  // validate person contacts
  personContact.map((contact) => {
    for (const key in contact) {
      if (Object.hasOwnProperty.call(contact, key)) {
        const element = contact[key];
        if (reg.test(element)) {
          error = { ErrorText };
        }
        setFormErrors(error);
      }
    }
  });

  // validate location
  if (location === "EditCompany") {
    // validate location for update company
    companyLocation.map((location) => {
      for (const key in location) {
        if (Object.hasOwnProperty.call(location, key)) {
          const element = location[key];
          if (reg.test(element)) {
            error = { ErrorText };
          }
          setFormErrors(error);
        }
      }
    });
  } else {
    // validate location for add new company
    companyLocation.map((location) => {
      if (reg.test(location)) {
        error = { ErrorText };
      }
      setFormErrors(error);
    });
  }

  // validate industry
  if (companyIndustry.length > 0) {
    companyIndustry.map((industry) => {
      if (reg.test(industry)) {
        error = { ErrorText };
      }
      setFormErrors(error);
    });
  }

  // validate company invoicing info
  if (companyInvoicing.length > 0) {
    if (reg.test(companyInvoicing)) {
      error = { ErrorText };
    }
    setFormErrors(error);
  }

  // validate company vat
  if (companyVat.length > 0) {
    if (reg.test(companyVat)) {
      error = { ErrorText };
    }
    setFormErrors(error);
  }

  return error;
};

// export const validateHtmlTagsUpdateCompany = (values, setFormErrors) => {
//   let error = {};

//   // assign variables
//   const companyName = values.en.company_name;
//   const personContact = values.en.contacts;
//   const companyLocation = values.en.location;
//   const companyIndustry = values.industries;
//   const companyInvoicing = values.invoicing_info;
//   const companyVat = values.vat;

//   // validate company name
//   if (companyName.length === 0) {
//     error = { companyError };
//   }

//   // validate person contacts
//   personContact.map((contact) => {
//     for (const key in contact) {
//       if (Object.hasOwnProperty.call(contact, key)) {
//         const element = contact[key];
//         if (reg.test(element)) {
//           error = { ErrorText };
//         }
//         setFormErrors(error);
//       }
//     }
//   });

//   // validate location
//   companyLocation.map((location) => {
//     for (const key in location) {
//       if (Object.hasOwnProperty.call(location, key)) {
//         const element = location[key];
//         if (reg.test(element)) {
//           error = { ErrorText };
//         }
//         setFormErrors(error);
//       }
//     }
//   });

//   // validate industry
//   //   if (companyIndustry.length > 0) {
//   //     companyIndustry.map((industry) => {
//   //       if (reg.test(industry)) {
//   //         error = { ErrorText };
//   //       }
//   //       setFormErrors(error);
//   //     });
//   //   }

//   // validate company invoicing info
//   if (companyInvoicing.length > 0) {
//     if (reg.test(companyInvoicing)) {
//       error = { ErrorText };
//     }
//     setFormErrors(error);
//   }

//   // validate company vat
//   if (companyVat.length > 0) {
//     if (reg.test(companyVat)) {
//       error = { ErrorText };
//     }
//     setFormErrors(error);
//   }

//   return error;
// };
