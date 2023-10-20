import * as moment from "moment/moment";
import "moment/locale/et";

const handleCandidateJobHistoryCvData = (
  cvData,
  setFormValues,
  setSearchKeywords,
  candidateCvID
) => {
  const specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;
  const populateData = { jobs: [] };

  let cvCompanyName = []; // company name
  let cvCompanyDesignation = []; // company designation
  let cvJobDescription = []; // description
  let cvJobWorkPlace = []; // work place
  let cvStartTime = [];
  let cvEndTime = [];

  // STARTING DATES START
  let jobStartYear = [];
  let jobStartMonth = [];
  let jobStartDay = [];

  // ENDING DATES START
  let jobEndYear = [];
  let jobEndMonth = [];
  let jobEndDay = [];

  if (cvData) {
    for (let index = 0; index < cvData.data?.length; index++) {
      const element = cvData.data[index];
      if (element.includes("TÖÖETTEVÕTTENIMI")) {
        cvCompanyName.push(element[1]);
      }

      if (element.includes("TÖÖAMET")) {
        cvCompanyDesignation.push(element[1]);
      }

      if (element.includes("TÖÖASUKOHT")) {
        cvJobWorkPlace.push(element[1]);
      }

      if (element.includes("TÖÖÜLESANNETE_KIRJELDUS")) {
        cvJobDescription.push(element[1]);
      }

      if (element.includes("TÖÖALGUSAASTA")) {
        if (element[1] !== ".") {
          jobStartYear.push(element[1]);
        }
      }

      if (element.includes("TÖÖALGUSKUU")) {
        let etToEnStartMonth = moment(element[1], "MM", "et").format("M");
        jobStartMonth.push(etToEnStartMonth);
      }

      if (element.includes("TÖÖALGUSPÄEV")) {
        let etToEnEndDay = moment(element[1], "LL", "et").format("D");
        jobStartDay.push(etToEnEndDay);
      }

      if (element.includes("TÖÖLÕPPAASTA")) {
        if (element[1] !== ".") {
          jobEndYear.push(element[1]);
        }
      }

      if (element.includes("TÖÖLÕPPKUU")) {
        let etToEnStartMonth = moment(element[1], "MMMM", "et").format("M");
        jobEndMonth.push(etToEnStartMonth);
      }

      if (element.includes("TÖÖLÕPP-PÄEV")) {
        if (element[1] !== ".") {
          let etToEnEndDay = moment(element[1], "LL", "et").format("D");
          jobEndDay.push(etToEnEndDay);
        }
      }
    }

    for (let i = 0; i < cvCompanyName.length; i++) {
      populateData.jobs.push({
        candidatecv_id: candidateCvID,
        company_name: cvCompanyName[i],
        designation: cvCompanyDesignation[i],
        work_place: cvJobWorkPlace[i],
        description:
          cvJobDescription[i] === undefined ? "" : cvJobDescription[i],
        starting_year:
          jobStartYear[i] !== "Invalid date" ? jobStartYear[i] : "",
        starting_month:
          jobStartMonth[i] !== "Invalid date" ? jobStartMonth[i] : "",
        starting_day: jobStartDay[i] !== "Invalid date" ? jobStartDay[i] : "",
        ending_year: jobEndYear[i] !== "Invalid date" ? jobEndYear[i] : "",
        ending_month: jobEndMonth[i] !== "Invalid date" ? jobEndMonth[i] : "",
        ending_day: jobEndDay[i] !== "Invalid date" ? jobEndDay[i] : "",
      });
    }
    setFormValues(populateData);

    // EXTRACT CV DATA FOR HIGHLIGHT TEXT
    let extractValues = [];

    if (cvCompanyName !== undefined) {
      extractValues.push(...cvCompanyName);
    }

    if (cvCompanyDesignation !== undefined) {
      extractValues.push(...cvCompanyDesignation);
    }

    if (cvJobDescription !== undefined && cvJobDescription.length > 0) {
      let jobDescriptionArr = [];
      cvJobDescription?.map((item) => {
        item.match(/.{1,20}(?:\s|$)/g).map((innerItem) => {
          jobDescriptionArr.push(innerItem);
        });
      });
      let jobDescriptionReplace = jobDescriptionArr.map((item) => {
        return item.replaceAll(specialCharacters, "");
      });
      let jobDescription = jobDescriptionReplace.map((item) => {
        return item.trim();
      });
      extractValues.push(...jobDescription);
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

export default handleCandidateJobHistoryCvData;
