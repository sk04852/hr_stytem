const handleCheckbox = (e, index, values, setEndMonth, setEndDay) => {
  let elements = document.getElementsByName(`jobs.${index}.ending_year`);
  let endMonthElement = document.getElementsByName(
    `jobs.${index}.ending_month`
  );
  let endDayElement = document.getElementsByName(`jobs.${index}.ending_day`);

  let jobsEndingValues = values.jobs[index];

  if (elements.length > 0) {
    for (let i = 0; i < elements.length; i++) {
      if (e.target.checked) {
        elements[i].disabled = true;
        elements[i].value = "";
        jobsEndingValues.ending_year = "";
      } else {
        elements[i].disabled = false;
      }
    }
  }
  if (endMonthElement.length > 0) {
    for (let i = 0; i < endMonthElement.length; i++) {
      if (e.target.checked) {
        endMonthElement[i].disabled = true;
        endMonthElement[i].value = "";
      } else {
        setEndMonth(null);
      }
    }
  }
  if (endDayElement.length > 0) {
    for (let i = 0; i < endDayElement.length; i++) {
      if (e.target.checked) {
        endDayElement[i].disabled = true;
        endDayElement[i].value = "";
      } else {
        setEndDay(null);
      }
    }
  }
};

export default handleCheckbox;
