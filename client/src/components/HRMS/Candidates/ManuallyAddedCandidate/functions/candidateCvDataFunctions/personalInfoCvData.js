import { highlightFieldColor, whiteColor } from "../../../../../Shared/Colors";
import * as moment from "moment";
import "moment/locale/et";
import estoniaCities from "../../../../../Shared/EstoniaCities.json";

const handleCandidatePersonalInfoCvData = (
  cvData,
  formValues,
  setFormValues,
  sethighlightDrivingLicenseField,
  sethighlightRecommendationsField,
  tags,
  setTags,
  setSearchKeywords
) => {
  const specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;

  let candidateFirstName = "";
  let candidateLastName = "";
  let cvGender = "";
  let date = [];
  let month = "";
  let year = "";
  let cvPersonalInfo = "";
  let cvPersonalCode = "";
  let cvPhone = "";
  let cvEmail = "";
  let motherLanguage = "";
  let cvLocation = "";
  let cvMaritalStatus = "";
  let cvDesiredJob = [];
  let cvDesiredWorkingTime = "";
  let cvDesiredLocation = [];
  let cvDesiredSalary = "";
  let cvDrivingLicenses = [];
  let cvRecommenders = [];

  // populate cv data in fields
  for (let index = 0; index < cvData?.data?.length; index++) {
    const element = cvData.data[index];
    if (element.includes("EESNIMI")) {
      candidateFirstName = element[1];
    }

    if (element.includes("PEREKONNANIMI")) {
      candidateLastName = element[1];
    }

    if (element.includes("SUGU")) {
      cvGender = element[1];
    }

    if (element.includes("SÜNNIPÄEV")) {
      date.push(element[1]);
    }

    if (element.includes("SÜNNIKUU")) {
      month = element[1];
    }

    if (element.includes("SÜNNIAASTA")) {
      year = element[1];
    }

    if (element.includes("ISIKLIK_INFO")) {
      cvPersonalInfo = element[1];
    }

    if (element.includes("ISIKUKOOD")) {
      cvPersonalCode = element[1];
    }

    if (element.includes("TELEFON")) {
      cvPhone = element[1];
    }

    if (element.includes("EMAIL")) {
      cvEmail = element[1];
    }

    if (element.includes("EMAKEEL")) {
      motherLanguage = element[1];
    }

    if (element.includes("AADRESS")) {
      cvLocation = element[1];
    }

    if (element.includes("PEREKONNASEIS")) {
      cvMaritalStatus = element[1];
    }

    if (element.includes("SOOVITUD_TÖÖKOHT")) {
      cvDesiredJob.push(element[1]);
    }

    if (element.includes("SOOVITUD_TÖÖ_AEG")) {
      cvDesiredWorkingTime = element[1];
    }

    if (element.includes("SOOVITUD_TÖÖ_ASUKOHT")) {
      cvDesiredLocation.push(element[1]);
    }

    if (element.includes("SOOVITUD_TÖÖTASU")) {
      cvDesiredSalary = element[1];
    }

    if (element.includes("JUHILOAD")) {
      cvDrivingLicenses.push(element[1]);
    }

    if (element.includes("SOOVITAJAD")) {
      cvRecommenders.push(element[1]);
    }
  }

  // date of birth start
  const DOB = year.concat("-", month, "-", date[0]);
  const etToEnDOB = moment(DOB, "YYYY-MMM-DD", "et").format("YYYY-MM-DD");
  // date of birth end

  // personal information with month language
  const personalInformationString =
    cvPersonalInfo.length > 0
      ? cvPersonalInfo.concat("\n\n", `Emakeel: ${motherLanguage}`)
      : "\n" + `Emakeel: ${motherLanguage}`;

  // desired jobs
  const desiredJobString = cvDesiredJob.toString().replaceAll(",", ", ");
  const desiredLocationString = cvDesiredLocation
    .toString()
    .replaceAll(",", ", ");

  // driving licenses
  if (cvDrivingLicenses.length > 0 && cvDrivingLicenses !== "") {
    for (let license of cvDrivingLicenses) {
      if (formValues.hasOwnProperty("driving_licenses")) {
        formValues.driving_licenses.push({
          level: license,
        });
      } else {
        formValues.driving_licenses = [];
        formValues.driving_licenses.push({
          level: license,
        });
      }
    }
    sethighlightDrivingLicenseField(cvDrivingLicenses);
  }

  // recommendations
  if (cvRecommenders && cvRecommenders.length > 0) {
    if (formValues.hasOwnProperty("recommendations")) {
      formValues.recommendations.push(cvRecommenders);
    } else {
      formValues.recommendations = [];
      formValues.recommendations.push(cvRecommenders);
    }
    sethighlightRecommendationsField(cvRecommenders);
  }

  // calculate AGE
  if (etToEnDOB !== null) {
    let today = new Date();
    let birthDate = new Date(etToEnDOB);
    let age = today.getFullYear() - birthDate.getFullYear();
    let m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    if (age > 0) {
      formValues.age = age;
    } else {
      formValues.age = "";
    }
  }

  // let cvNationality = cvData.RAHVUS === undefined ? "" : cvData.RAHVUS; // change all nationalities languages into estonian
  // if (cvNationality.length > 0) {
  //   for (let nationality of cvNationality) {
  //     if (formValues.hasOwnProperty("nationalities")) {
  //       formValues.nationalities.push(nationality);
  //     } else {
  //       formValues.nationalities = [];
  //       formValues.nationalities.push(nationality);
  //     }
  //   }
  // }

  // let cvKeywords = cvData.MÄRKSÕNAD === undefined ? "" : cvData.MÄRKSÕNAD; // does't not exist
  // if (cvKeywords.length > 0) {
  //   for (let keyword of cvKeywords) {
  //     if (formValues.hasOwnProperty("keywords")) {
  //       formValues.keywords.push(keyword);
  //     } else {
  //       formValues.keywords = [];
  //       formValues.keywords.push(keyword);
  //     }
  //   }
  // }

  if (candidateFirstName && candidateFirstName.length > 0) {
    let elem = document.getElementById("first_name");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (candidateLastName && candidateLastName.length > 0) {
    let elem = document.getElementById("last_name");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvGender && cvGender.length > 0) {
    let elem = document.getElementById("gender");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (etToEnDOB && etToEnDOB.length > 0) {
    let elem = document.getElementById("dob");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvPersonalInfo && cvPersonalInfo.length > 0) {
    let elem = document.getElementById("personal-information");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvPersonalCode && cvPersonalCode.length > 0) {
    let elem = document.getElementById("personal-code");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvPhone && cvPhone.length > 0) {
    let elem = document.getElementById("phone");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvEmail && cvEmail.length > 0) {
    let elem = document.getElementById("email");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvLocation && cvLocation.length > 0) {
    let elem = document.getElementById("location");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvMaritalStatus && cvMaritalStatus.length > 0) {
    let elem = document.getElementById("marital-status");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (desiredJobString && desiredJobString.length > 0) {
    let elem = document.getElementById("desired-job");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvDesiredWorkingTime && cvDesiredWorkingTime.length > 0) {
    let elem = document.getElementById("desired-job-time");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (desiredLocationString && desiredLocationString.length > 0) {
    let elem = document.getElementById("desired-job-location");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  if (cvDesiredSalary && cvDesiredSalary.length > 0) {
    let elem = document.getElementById("desired-job-salary");
    if (elem) {
      elem.style.background = highlightFieldColor;
    } else {
      elem.style.background = whiteColor;
    }
  }

  setFormValues({
    ...formValues,
    first_name: candidateFirstName === undefined ? "" : candidateFirstName,
    last_name: candidateLastName === undefined ? "" : candidateLastName,
    gender:
      cvGender === "naine" || cvGender === "Naine"
        ? "2"
        : cvGender === "mees" || cvGender === "Mees"
        ? "1"
        : "",
    dob: etToEnDOB === undefined ? "" : etToEnDOB,
    personal_information:
      cvPersonalInfo === undefined ? "" : personalInformationString,
    personal_code: cvPersonalCode === undefined ? "" : cvPersonalCode,
    phone: cvPhone === undefined ? "" : cvPhone,
    email: cvEmail === undefined ? "" : cvEmail,
    location: cvLocation === undefined ? "" : cvLocation,
    marital_status: cvMaritalStatus === undefined ? "" : cvMaritalStatus,
    desired_job: cvDesiredJob === undefined ? "" : desiredJobString,
    desired_salary: cvDesiredSalary === undefined ? "" : cvDesiredSalary,
    desired_job_time:
      cvDesiredWorkingTime === undefined ? "" : cvDesiredWorkingTime,
    desired_job_location:
      desiredLocationString === undefined || "" ? "" : desiredLocationString,
  });

  // EXTRACT KEYWORDS INTO TAGS
  let extractedKeywords = [...tags];
  if (cvGender) {
    extractedKeywords.push(cvGender);
  }

  if (cvLocation) {
    let replacedCharacters = cvLocation
      ?.replaceAll(specialCharacters, " ")
      .trim();
    replacedCharacters?.split(" ").map((locationItem) => {
      estoniaCities.map((city) => {
        if (locationItem === city.city) {
          if (!extractedKeywords.includes(city.city)) {
            extractedKeywords.push(city.city);
          }
        }
      });
    });
  }

  if (cvDesiredJob) {
    extractedKeywords.push(...cvDesiredJob);
  }

  if (cvDesiredSalary) {
    extractedKeywords.push(cvDesiredSalary);
  }

  if (cvDesiredWorkingTime) {
    extractedKeywords.push(cvDesiredWorkingTime);
  }

  if (cvDesiredLocation) {
    extractedKeywords.push(...cvDesiredLocation);
  }

  if (cvDrivingLicenses && cvDrivingLicenses.length > 0) {
    extractedKeywords.push(...cvDrivingLicenses);
  }

  let uniqueKeywords = [...new Set(extractedKeywords)];
  setTags(uniqueKeywords);

  // EXTRACT CV DATA FOR HIGHLIGHT TEXT
  let extractValues = [];

  if (cvLocation !== undefined) {
    let cvLocationSplit = cvLocation.split(" ");
    let filter = cvLocationSplit.filter((item) => {
      return item != "," && item.length > 2;
    });
    extractValues.push(...filter);
  }

  if (candidateFirstName !== undefined) {
    extractValues.push(candidateFirstName);
  }

  if (candidateLastName !== undefined) {
    extractValues.push(candidateLastName);
  }

  if (cvPhone !== undefined) {
    let newString = cvPhone.replace("+", "");
    extractValues.push(newString);
  }

  if (cvEmail !== undefined) {
    extractValues.push(cvEmail.split(" ")[0]);
  }

  if (cvGender !== undefined) {
    extractValues.push(cvGender);
  }

  if (DOB !== undefined) {
    let dob = DOB.replaceAll("-", " ").split(" ");
    if (dob.length > 0) {
      extractValues.push(...dob);
    }
  }

  if (cvPersonalInfo !== undefined && cvPersonalInfo.length > 0) {
    let personalInfoSplitArr = cvPersonalInfo.replaceAll(" , ", " ").split(" ");
    let personalInfoReplace = personalInfoSplitArr.map((item) => {
      return item.replaceAll(specialCharacters, "");
    });
    let personalInfo = personalInfoReplace.filter((item) => {
      return item.length > 2;
    });
    if (personalInfo.length > 0) {
      extractValues.push(...personalInfo);
    }
  }

  if (cvPersonalCode !== undefined) {
    extractValues.push(cvPersonalCode);
  }

  if (cvMaritalStatus !== undefined) {
    extractValues.push(cvMaritalStatus);
  }

  if (cvDesiredJob !== undefined) {
    extractValues.push(...cvDesiredJob);
  }

  if (cvDesiredSalary !== undefined) {
    extractValues.push(cvDesiredSalary);
  }

  if (cvDesiredLocation !== undefined) {
    extractValues.push(...cvDesiredLocation);
  }

  if (cvDesiredWorkingTime !== undefined) {
    extractValues.push(cvDesiredWorkingTime);
  }

  // if (cvDrivingLicenses !== undefined) {
  //   extractValues.push(...cvDrivingLicenses);
  // }

  if (cvRecommenders && cvRecommenders.lenght > 0) {
    console.log(cvRecommenders);
    extractValues.push(...cvRecommenders);
    // let recommenders = cvRecommenders.split(" • ");
    // if (recommenders.lenght > 0) {
    //   extractValues.push(...recommenders);
    // }
  }

  let uniqueExtractedValues = [...new Set(extractValues)];
  let filteredExtractedValues = uniqueExtractedValues?.filter((item) => {
    return item != "";
  });
  if (filteredExtractedValues.length > 0) {
    setSearchKeywords(filteredExtractedValues);
  }
};

export default handleCandidatePersonalInfoCvData;
