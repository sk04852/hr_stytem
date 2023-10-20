import * as moment from "moment/moment";
import "moment/locale/et";

const handleCandidateEducationCvData = (
  cvData,
  setFormValues,
  setSearchKeywords,
  educationDegrees,
  candidateCvID
) => {
  if (cvData) {
    let cvInstitute = [];
    let cvEducationLevel = [];
    let cvSpeciality = [];
    let cvAdditionalInformation = [];
    let cvStartTime = [];
    let cvEndTime = [];

    const startingYear = [];
    const startingMonth = [];
    const startingDay = [];

    const endingYear = [];
    const endingMonth = [];
    const endingDay = [];

    for (let index = 0; index < cvData?.data?.length; index++) {
      const element = cvData.data[index];

      if (element.includes("HARIDUSASUTUS")) {
        cvInstitute.push(element[1]);
      }

      if (element.includes("HARIDUSERIALA")) {
        cvSpeciality.push(element[1]);
      }

      if (element.includes("HARIDUSALGUSAASTA")) {
        if (element[1] !== ".") {
          startingYear.push(element[1]);
        }
      }

      if (element.includes("HARIDUSALGUSKUU")) {
        startingMonth.push(element[1]);
      }

      if (element.includes("HARIDUSALGUSPÄEV")) {
        startingDay.push(element[1]);
      }

      if (element.includes("HARIDUSLÕPPAASTA")) {
        endingYear.push(element[1]);
      }

      if (element.includes("HARIDUSLÕPPKUU")) {
        endingMonth.push(element[1]);
      }

      if (element.includes("HARIDUSLÕPPPÄEV")) {
        endingDay.push(element[1]);
      }

      if (element.includes("HARIDUSLISAINFO")) {
        cvAdditionalInformation.push(element[1]);
      }
    }

    let populateData = { education: [] };

    const educationLevelIds = [];
    if (cvEducationLevel.length > 0) {
      for (let educationLevelName of cvEducationLevel) {
        educationDegrees.find((items) => {
          if (
            items.name === educationLevelName.toLowerCase() ||
            educationLevelName.toUpperCase()
          ) {
            educationLevelIds.push(items.name);
          }
        });
      }
    }

    for (let i = 0; i < cvInstitute.length; i++) {
      populateData.education.push({
        candidatecv_id: candidateCvID,
        institute: cvInstitute[i],
        // degree: educationLevelIds[i],
        speciality: cvSpeciality[i],
        starting_year:
          startingYear[i] !== "Invalid date" || undefined ? startingDay[i] : "",
        starting_month:
          startingMonth[i] !== "Invalid date" || undefined
            ? startingMonth[i]
            : "",
        starting_day:
          startingDay[i] !== "Invalid date" || undefined ? startingDay[i] : "",
        ending_year:
          endingYear[i] !== "Invalid date" || undefined ? endingYear[i] : "",
        ending_month:
          endingMonth[i] !== "Invalid date" || undefined ? endingMonth[i] : "",
        ending_day:
          endingDay[i] !== "Invalid date" || undefined ? endingDay[i] : "",
        additonal_information: cvAdditionalInformation[i],
      });
    }
    setFormValues(populateData);

    // EXTRACT CV DATA FOR HIGHLIGHT TEXT
    let extractValues = [];

    if (cvInstitute !== undefined) {
      extractValues.push(...cvInstitute);
    }

    if (cvEducationLevel !== undefined) {
      extractValues.push(...cvEducationLevel);
    }

    if (cvSpeciality !== undefined) {
      extractValues.push(...cvSpeciality);
    }

    if (cvStartTime !== undefined) {
      extractValues.push(...cvStartTime);
    }

    if (cvEndTime !== undefined) {
      extractValues.push(...cvEndTime);
    }

    let uniqueExtractedValues = [...new Set(extractValues)];
    let filteredExtractedValues = uniqueExtractedValues?.filter((item) => {
      return item != "";
    });
    if (filteredExtractedValues.length > 0) {
      setSearchKeywords(filteredExtractedValues);
    }
  }
};

export default handleCandidateEducationCvData;
