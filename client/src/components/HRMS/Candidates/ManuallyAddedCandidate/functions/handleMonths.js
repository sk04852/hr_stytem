export const handleStartMonths = (e, setStartMonth) => {
  const selectedMonth = e.target.value;
  if (selectedMonth === "1") {
    setStartMonth(0);
  } else if (selectedMonth === "2") {
    setStartMonth(1);
  } else if (selectedMonth === "3") {
    setStartMonth(2);
  } else if (selectedMonth === "4") {
    setStartMonth(3);
  } else if (selectedMonth === "5") {
    setStartMonth(4);
  } else if (selectedMonth === "6") {
    setStartMonth(5);
  } else if (selectedMonth === "7") {
    setStartMonth(6);
  } else if (selectedMonth === "8") {
    setStartMonth(7);
  } else if (selectedMonth === "9") {
    setStartMonth(8);
  } else if (selectedMonth === "10") {
    setStartMonth(9);
  } else if (selectedMonth === "11") {
    setStartMonth(10);
  } else if (selectedMonth === "12") {
    setStartMonth(11);
  } else {
    setStartMonth(null);
  }
};

export const handleEndMonths = (e, setEndMonth) => {
  const selectedMonth = e.target.value;
  if (selectedMonth === "1") {
    setEndMonth(0);
  } else if (selectedMonth === "2") {
    setEndMonth(1);
  } else if (selectedMonth === "3") {
    setEndMonth(2);
  } else if (selectedMonth === "4") {
    setEndMonth(3);
  } else if (selectedMonth === "5") {
    setEndMonth(4);
  } else if (selectedMonth === "6") {
    setEndMonth(5);
  } else if (selectedMonth === "7") {
    setEndMonth(6);
  } else if (selectedMonth === "8") {
    setEndMonth(7);
  } else if (selectedMonth === "9") {
    setEndMonth(8);
  } else if (selectedMonth === "10") {
    setEndMonth(9);
  } else if (selectedMonth === "11") {
    setEndMonth(10);
  } else if (selectedMonth === "12") {
    setEndMonth(11);
  } else {
    setEndMonth(null);
  }
};
