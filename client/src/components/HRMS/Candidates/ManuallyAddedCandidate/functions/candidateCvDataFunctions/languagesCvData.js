const handleCandidateCvDataLanguages = (
  cvData,
  setFormValues,
  setSearchKeywords,
  candidateCvID
) => {
  let populateData = {
    candidatecv_id: candidateCvID,
    language: [],
  };
  let cvLanguage = []; // language names

  for (let index = 0; index < cvData.data?.length; index++) {
    const element = cvData.data[index];

    if (element.includes("KEEL")) {
      cvLanguage.push(element[1]);
    }
  }

  if (cvLanguage.length > 0) {
    for (let i = 0; i < cvLanguage.length; i++) {
      populateData.language.push({
        name: cvLanguage[i],
      });
    }
  }
  setFormValues(populateData);

  // EXTRACT CV DATA FOR HIGHLIGHT TEXT
  let extractValues = [];

  if (cvLanguage !== undefined && cvLanguage.length > 0) {
    extractValues.push(...cvLanguage);
  }

  let uniqueExtractedValues = [...new Set(extractValues)];
  let filteredExtractedValues = uniqueExtractedValues?.filter((item) => {
    return item != "";
  });
  if (filteredExtractedValues.length > 0) {
    setSearchKeywords(filteredExtractedValues);
  }
};

export default handleCandidateCvDataLanguages;
