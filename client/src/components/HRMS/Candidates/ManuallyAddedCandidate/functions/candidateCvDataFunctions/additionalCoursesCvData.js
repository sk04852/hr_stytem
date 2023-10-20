const handleCandidateAdditionalCoursesCvData = (
  cvData,
  setFormValues,
  setSearchKeywords
) => {
  let specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;
  let courseData = { courses: [] };

  //   let courseTitle = cvData.TK_NIMETUS === undefined ? "" : cvData.TK_NIMETUS;
  //   let courseDescription = cvData.TK_KIRJELDUS === undefined ? "" : cvData.TK_KIRJELDUS;
  //   let courseStartTime = cvData.TK_ALGUS === undefined ? "" : cvData.TK_ALGUS;
  let courseTitle = [];
  let courseDescription = [];
  let courseTotalHours = [];
  let courseStartDate = [];

  for (let index = 0; index < cvData.data?.length; index++) {
    const element = cvData.data[index];

    if (element.includes("TÄIENDKOOLITUS_NIMETUS")) {
      courseTitle.push(element[1]);
    }

    if (element.includes("TÄIENDKOOLITUSKOOLITAJA")) {
      courseDescription.push(element[1]);
    }

    if (element.includes("TÄIENDKOOLITUSMAHT")) {
      courseTotalHours.push(element[1]);
    }

    if (element.includes("TÄIENDKOOLITUSALGUSKUU")) {
      if (element[1] !== ".") {
        courseStartDate.push(element[1]);
      }
    }
  }

  for (let i = 0; i < courseTitle.length; i++) {
    courseData.courses.push({
      title: courseTitle[i],
      description: courseDescription[i],
      starting_year: courseStartDate[i],
      // total_hours: courseTotalHours[i],
      starting_month: "",
      starting_day: "",
      ending_year: "",
      ending_month: "",
      ending_day: "",
    });
  }

  setFormValues(courseData);

  // EXTRACT CV DATA FOR HIGHLIGHT TEXT
  let extractValues = [];

  if (courseTitle !== undefined && courseTitle.length > 0) {
    let courseTitleMatch = courseTitle.join().match(/.{1,30}(?:\s|$)/g);
    let replaceTitleString = courseTitleMatch.map((item) => {
      return item.replaceAll(specialCharacters, "");
    });
    let courseTitleArr = replaceTitleString.map((item) => {
      return item.trim();
    });
    extractValues.push(...courseTitleArr);
  }

  if (courseDescription !== undefined && courseDescription.length > 0) {
    extractValues.push(...courseDescription);
  }

  //   if (courseStartTime !== undefined && courseStartTime.length > 0) {
  //     let courseStartTimeArr = [];
  //     for (let i = 0; i < courseStartTime.length; i++) {
  //       const element = courseStartTime[i];
  //       const split = element.split(" • ");
  //       for (const key in split) {
  //         if (Object.hasOwnProperty.call(split, key)) {
  //           const element = split[key];
  //           courseStartTimeArr.push(element);
  //         }
  //       }
  //     }
  //     // courseStartTimeArr
  //     extractValues.push(...courseStartTimeArr);
  //   }

  let uniqueExtractedValues = [...new Set(extractValues)];
  let filteredExtractedValues = uniqueExtractedValues?.filter((item) => {
    return item != "";
  });
  if (filteredExtractedValues.length > 0) {
    setSearchKeywords(filteredExtractedValues);
  }
};

export default handleCandidateAdditionalCoursesCvData;
