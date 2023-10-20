import {
  highlightClickedFieldColor,
  highlightFieldColor,
  whiteColor,
} from "../../../../Shared/Colors";

export const handleClickedSearchKeywords = (
  e,
  cvData,
  setClickedSearchKeywords
) => {
  let specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;
  if (cvData) {
    let value = e.target.value;
    let fieldId = e.target.id;

    let clickedArr = [];

    let fieldElem = document.getElementById(fieldId);
    if (fieldElem) {
      fieldElem.style.background = highlightClickedFieldColor;
    }

    if (fieldId === "gender") {
      // gender
      if (value === "1") {
        value = "Mees";
        clickedArr.push(value);
      } else if (value === "2") {
        value = "Naine";
        clickedArr.push(value);
      }
    } else if (fieldId === "dob") {
      // date of birth
      let dobValue = value.split("-");
      if (dobValue[1] === "01") {
        dobValue[1] = "jaanuar";
      } else if (dobValue[1] === "02") {
        dobValue[1] = "veebruar";
      } else if (dobValue[1] === "03") {
        dobValue[1] = "märts";
      } else if (dobValue[1] === "04") {
        dobValue[1] = "aprill";
      } else if (dobValue[1] === "05") {
        dobValue[1] = "mai";
      } else if (dobValue[1] === "06") {
        dobValue[1] = "juuni";
      } else if (dobValue[1] === "07") {
        dobValue[1] = "juuli";
      } else if (dobValue[1] === "08") {
        dobValue[1] = "august";
      } else if (dobValue[1] === "09") {
        dobValue[1] = "september";
      } else if (dobValue[1] === "10") {
        dobValue[1] = "oktoober";
      } else if (dobValue[1] === "11") {
        dobValue[1] = "november";
      } else if (dobValue[1] === "12") {
        dobValue[1] = "detsember";
      }

      for (let i = 0; i < dobValue.length; i++) {
        clickedArr.push(dobValue[i]);
      }
    } else if (fieldId === "personal-information") {
      // personal information
      value = value.replace(new RegExp(" ", "gi"), "|").split("|");
      const filterValuesArr = value.filter((item) => {
        return item != "" && item.length > 2;
      });
      for (let i = 0; i < filterValuesArr.length; i++) {
        clickedArr.push(filterValuesArr[i]);
      }
    } else if (fieldId === "phone") {
      // phone
      let newValue = value.replaceAll("+", "");
      newValue.split(" ").map((item) => {
        clickedArr.push(item);
      });
    } else if (fieldId === "email") {
      // email
      let newEmailValue = value.replaceAll(specialCharacters, " ");
      let replaceEmailValue = newEmailValue.replace("@", " ");
      replaceEmailValue.split(" ").map((item) => {
        clickedArr.push(item);
      });
    } else if (fieldId === "location") {
      // location
      let filterLocationArr = value.split(" ").filter((item) => {
        return item !== "";
      });
      filterLocationArr.map((item) => {
        clickedArr.push(item);
      });
    } else {
      clickedArr.push(value);
    }
    setClickedSearchKeywords(clickedArr);
  }
};

export const handleClickedBlurSearchKeywords = (
  e,
  cvData,
  setClickedSearchKeywords
) => {
  if (cvData) {
    let fieldId = e.target.id;
    setClickedSearchKeywords([]);
    let fieldElem = document.getElementById(fieldId);
    let selectedString = "";
    for (let index = 0; index < cvData.length; index++) {
      const element = cvData[index];
      if (element.includes(fieldElem.value)) {
        selectedString = element[1];
      }
    }

    if (fieldId === "personal-information" && fieldElem.value.length > 0) {
      fieldElem.style.background = highlightFieldColor;
    } else if (selectedString && selectedString.length > 0) {
      fieldElem.style.background = highlightFieldColor;
    } else {
      fieldElem.style.background = whiteColor;
    }
  }
};
