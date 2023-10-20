import React, { useCallback, useEffect, useState } from "react";
import { useHistory } from "react-router-dom";
import * as moment from "moment";
import { useDispatch, useSelector } from "react-redux";
import {
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Container,
  Table,
} from "reactstrap";
import {
  deleteJobs,
  duplicateJob,
  getAllJobs,
} from "../../../redux/actions/jobsAction";
import Toast from "../../constants/toast";
import { DataLoader } from "../../constants/loaders";
import { NoRecordFound } from "../../constants/noRecordFound";
import Paginator from "react-hooks-paginator";
import { useTranslation } from "react-i18next";

const Jobs = (props) => {
  const { fixNavbar } = props;
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const initialValues = {
    title: "",
    company: "",
    location: "",
    job_type: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  // const [titleFilter, setTitleFilter] = useState([]);
  // const [jobTypeFilter, setJobTypeFilter] = useState([]);
  // const [companyFilter, setcompanyFilter] = useState([]);
  // const [locationFilter, setLocationFilter] = useState([]);
  const [data, setData] = useState([]);
  const [userPermission, setUserPermission] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  // PAGINATION STATES START
  const [offset, setOffset] = useState(0);
  const [pageNumber, setPageNumber] = useState(1);
  const [totalRecords, setTotalRecords] = useState(0);
  // PAGINATION STATES END

  const [sorting, setSorting] = useState(null);

  const fetchAllJobs = async (title, job_type, company, location) => {
    try {
      const response = await dispatch(
        getAllJobs(
          pageNumber,
          searchTerm,
          title,
          job_type,
          company,
          location,
          sorting
        )
      );

      // let jobsResponse = response.payload.data;
      let jobsData = response.payload.data.data;
      let jobsDataLength = response.payload.data.data.length;
      let totalJobs = response.payload.data.total;

      if (jobsDataLength === 0) {
        setData([]);
      } else {
        setData(jobsData);
        setTotalRecords(totalJobs);

        // FILTERS
        // let titleFilterArr = [];
        // let locationFilterArr = [];
        // const titleFilter = [
        //   ...new Set(
        //     jobsData.map((item) => {
        //       if (item.length > 0) {
        //         item.map((jobItem) => {
        //           titleFilterArr.push(jobItem.offer_name);
        //           locationFilterArr.push(jobItem.location);
        //         });
        //       }
        //     })
        //   ),
        // ];

        // let uniqueTitleFilterArr = [...new Set(titleFilterArr)];
        // let uniqueLocationFilterArr = [...new Set(locationFilterArr)];
        //
        // const jobTypeFilter = [
        //   ...new Set(jobsData.map((item) => item.job_type)),
        // ];

        // let companyFilterArr = [];

        // const companyFilter = [
        //   ...new Set(
        //     jobsData.map((item) => {
        //       if (
        //         item.companyPr.company !== null &&
        //         item.companyPr.company.length > 0
        //       ) {
        //         item.companyPr.company.map((companyName) => {
        //           companyFilterArr.push(companyName.name);
        //         });
        //       }
        //     })
        //   ),
        // ];
        // let uniqueCompanyFilterArr = [...new Set(companyFilterArr)];

        // setTitleFilter(uniqueTitleFilterArr);
        // setJobTypeFilter(jobTypeFilter);
        // setcompanyFilter(uniqueCompanyFilterArr);
        // setLocationFilter(uniqueLocationFilterArr);
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAllJobs();
  }, [pageNumber, searchTerm, sorting]);

  // const getJobsSelector = useSelector((state) => {
  //   return state.jobs.jobs_data;
  // });
  const jobsSelector = useSelector((state) => {
    return state.jobs;
  });
  const settingsSelector = useSelector((state) => state.settings);

  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getUserProfileSelector]);

  // const inactiveSorting = (data) => {
  //   let upData = data.filter((u) => u.status == "1");
  //   let downData = data.filter((u) => u.status == "0" || u.status == "2");

  //   upData.push(...downData);
  //   setData(upData);
  // };

  for (let i = 0; i < data.length; i++) {
    if (data[i].deadline && typeof data[i].deadline === "string") {
      let given = moment(data[i].deadline, "YYYY-MM-DD");
      let current = moment().startOf("day");
      let deadlineDays = moment.duration(given.diff(current)).asDays();

      let updatedDate = data[i].deadline.split(" ")[0].substring(8);

      updatedDate = Math.round(deadlineDays);

      if (updatedDate > 0) {
        data[i].deadline = updatedDate;
        if (data[i].deadline <= 3 && data[i].deadline >= 0) {
          data[i] = {
            ...data[i],
            deadlineColor: "#E8769F",
          };
        } else if (data[i].deadline > 3 && data[i].deadline < 10) {
          data[i] = {
            ...data[i],
            deadlineColor: "#FBBD08",
          };
        } else {
          data[i] = {
            ...data[i],
            deadlineColor: "#CEDD7A",
          };
        }
      } else {
        data[i].deadline = "0";
        data[i].status = "0";
        data[i] = {
          ...data[i],
          deadlineColor: "#E8769F",
        };
      }
    }
  }

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      // const sorted = [...data]?.sort((a, b) => (a[e] > b[e] ? 1 : -1));
      // setData(sorted);
      setSortingOrder("dsc");
      setSorting("-" + e);
    }
    if (sortingOrder === "dsc") {
      // const sorted = [...data]?.sort((a, b) => (a[e] < b[e] ? 1 : -1));
      // setData(sorted);
      setSortingOrder("asc");
      setSorting(e);
    }
    // }
  };

  // const jobFilters = (title, jobType, company, location) => {
  //   let updatedItems = getJobsSelector.data.data.filter((curElem) => {
  //     // console.log(curElem);
  //     // console.log(title, jobType, company, location);
  //     return (
  //       curElem.title === title ||
  //       (curElem.title === title && curElem.job_type === jobType) ||
  //       (curElem.job_type === jobType && curElem.company === company) ||
  //       (curElem.company === company && curElem.location === location) ||
  //       curElem.location === location
  //     );
  //   });
  //   setData(updatedItems);
  // };

  const resetFilters = () => {
    let elements = document.getElementById("selectFilters");
    elements.selectedIndex = 0;
    let elements2 = document.getElementById("selectFilters1");
    elements2.selectedIndex = 0;
    let elements3 = document.getElementById("selectFilters2");
    elements3.selectedIndex = 0;
    let elements4 = document.getElementById("selectFilters3");
    elements4.selectedIndex = 0;

    setFormValues(initialValues);
    // setData(fetchAllJobs("", "", "", ""));
    fetchAllJobs("", "", "", "");
  };

  const handleDelete = async (e, id) => {
    const confirm = window.confirm("Oled kindel?");
    if (confirm === true) {
      dispatch(deleteJobs(id, fetchAllJobs));
    }
  };

  const handleDuplicate = (id) => {
    // const duplicateRow = data[id];
    // const firstPart = data.slice(0, id + 1);
    // const secondPart = data.slice(id + 1, data.length);
    // const newData = [...firstPart, duplicateRow, ...secondPart];
    // document.getElementById("modalButton").click()
    // setDuplicaterow(newData)
    // let newData = data.filter((u) => u.id == id);
    dispatch(duplicateJob(id, fetchAllJobs));
    // data.push(...newData);
    // inactiveSorting(data);
  };

  const debounce = (func) => {
    let timer;
    return function (...args) {
      const context = this;
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => {
        timer = null;
        func.apply(context, args);
      }, 500);
    };
  };

  const handleSearchChange = (e) => {
    let searchValue = e.target.value;
    if (searchValue.length) {
      setSearchTerm(searchValue);
    }
  };

  const optimizedSearchFn = useCallback(debounce(handleSearchChange), []);

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} `}>
        <Container fluid={true}>
          <div className="d-flex justify-content-between align-items-center mb-3">
            {userPermission.includes("Can-Add-Jobs") ? (
              <div className="header-action w-100 text-right mt-2">
                <button
                  type="button"
                  className="btn btn-primary"
                  data-toggle="modal"
                  onClick={() => history.push("/hr-jobs/add-jobs")}
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "14px"
                    }`,
                  }}
                >
                  <i className="fe fe-plus mr-2" />
                  {t("jobs.addNewJobButton")}
                </button>
              </div>
            ) : null}
          </div>
          {/* FILTERS SECTION START */}
          {/* <Row className={"clearfix"}>
            <Col className="col-12">
              <Card>
                <CardBody>
                  <Row>
                    <Col lg={2} md={4} sm={6}>
                      <Label
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Title
                      </Label>
                      <div className="multiselect_div">
                        <select
                          className="custom-select"
                          id={"selectFilters"}
                          onChange={(event) => {
                            setFormValues({
                              ...formValues,
                              title: event.target.value,
                            });
                          }}
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <option value={0}>Ei ole valitud</option>
                          {titleFilter.map((item, index) => (
                            <option value={item} key={index}>
                              {item}
                            </option>
                          ))}
                        </select>
                      </div>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Renditöö vajadus
                      </Label>
                      <div className="form-group">
                        <select
                          className="custom-select"
                          id={"selectFilters1"}
                          onChange={(event) =>
                            setFormValues({
                              ...formValues,
                              job_type: event.target.value,
                            })
                          }
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <option value={0}>Ei ole valitud</option>
                          {jobTypeFilter.map((item, index) => (
                            <option value={item} key={index}>
                              {item}
                            </option>
                          ))}
                        </select>
                      </div>
                    </Col>

                    <Col lg={2} md={4} sm={6}>
                      <Label
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Ettevõte
                      </Label>
                      <div className="form-group">
                        <select
                          className="custom-select"
                          id={"selectFilters2"}
                          onChange={(event) =>
                            setFormValues({
                              ...formValues,
                              company: event.target.value,
                            })
                          }
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <option value={0}>Ei ole valitud</option>
                          {companyFilter.map((item, index) => (
                            <option value={item} key={index}>
                              {item}
                            </option>
                          ))}
                        </select>
                      </div>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Asukoht
                      </Label>
                      <div className="form-group">
                        <select
                          className="custom-select"
                          id={"selectFilters3"}
                          onChange={(event) =>
                            setFormValues({
                              ...formValues,
                              location: event.target.value,
                            })
                          }
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <option value={0}>Ei ole valitud</option>
                          {locationFilter.map((item, index) => (
                            <option value={item} key={index}>
                              {item}
                            </option>
                          ))}
                        </select>
                      </div>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label>&nbsp;</Label>
                      <Button
                        type={"button"}
                        className="btn btn-sm btn-primary btn-block mt-3"
                        onClick={() => {
                          fetchAllJobs(
                            formValues.title,
                            formValues.job_type,
                            formValues.company,
                            formValues.location
                          );
                        }}
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Filter
                      </Button>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label>&nbsp;</Label>
                      <Button
                        type={"reset"}
                        className="btn btn-sm btn-primary btn-block mt-3"
                        onClick={resetFilters}
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "0.875rem"
                          }`,
                        }}
                      >
                        Tühista
                      </Button>
                    </Col>
                  </Row>
                </CardBody>
              </Card>
            </Col>
          </Row> */}
          {/* FILTERS SECTION END */}
        </Container>
      </div>
      <div className="section-body">
        <Container fluid={true}>
          <div className="tab-content">
            <div
              className="tab-pane fade show active"
              id="Employee-list"
              role="tabpanel"
            >
              <Card>
                <CardHeader>
                  <CardTitle>
                    <h5
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "1.75rem"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "2.25rem"
                            : "1.25rem"
                        }`,
                      }}
                    >
                      {t("jobs.jobOffersList")}
                    </h5>
                  </CardTitle>
                  <div className="card-options">
                    {userPermission.includes("Can-Job-Search") ? (
                      <form>
                        <div className="input-group">
                          <input
                            type="text"
                            className="form-control form-control-sm"
                            placeholder="Search something..."
                            name="s"
                            onChange={optimizedSearchFn}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                        </div>
                      </form>
                    ) : null}
                  </div>
                </CardHeader>
                <CardBody>
                  <div
                    className="table-responsive"
                    style={{ overflowX: "auto" }}
                  >
                    <Table
                      id={"candidate-table"}
                      className="table table-hover table-striped table-vcenter mx-2 text-sm text-nowrap mb-0 table-sortable"
                      style={{ marginLeft: "-1.25rem" }}
                    >
                      <thead>
                        <tr>
                          <th
                            className={"no-sort"}
                            style={{
                              color: "#000",
                              marginTop: "5px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            #
                          </th>
                          <th
                            onClick={() => handleSorting("offer_name")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.offerName")}
                          </th>
                          <th
                            onClick={() => handleSorting("company_name")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.company.companyName")}
                          </th>
                          <th
                            onClick={() => handleSorting("created_at")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {/* Loomise kuupäev */}
                            {t("jobs.addNewJob.createdAt")}
                          </th>
                          <th
                            onClick={() => handleSorting("deadline")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.deadline")}
                          </th>
                          <th
                            onClick={() => handleSorting("status")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.status.status")}
                          </th>
                          <th
                            onClick={() => handleSorting("training")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.traning")}
                          </th>
                          <th
                            onClick={() => handleSorting("required_candidates")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {/* Otsitavate inimeste hulk */}
                            {t("jobs.addNewJob.numberOfPeoplesWanted")}
                          </th>
                          <th
                            onClick={() => handleSorting("job_applicats_count")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.jobApplicantsCount")}
                          </th>
                          <th
                            onClick={() => handleSorting("job_type")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {/* Renditöö vajadus */}
                            {t("jobs.addNewJob.jobType.jobType")}
                          </th>
                          <th
                            onClick={() => handleSorting("location")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "25px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.location")}
                          </th>
                          <th
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("actions")}
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        {!jobsSelector.isLoading ? (
                          data.length > 0 ? (
                            data.map((c, index) => (
                              <tr key={index}>
                                <td className="w40">
                                  <label className="custom-control custom-checkbox">
                                    <input
                                      type="checkbox"
                                      className="custom-control-input"
                                      name="example-checkbox1"
                                      defaultValue="option1"
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "14px"
                                        }`,
                                      }}
                                    />
                                    <span className="custom-control-label">
                                      &nbsp;
                                    </span>
                                  </label>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <h6
                                    className="mb-0"
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "15px"
                                      }`,
                                    }}
                                  >
                                    {c.offer_name}
                                  </h6>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  {c.company_name}
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  {c.created_at != null
                                    ? c.created_at.substring(0, 10)
                                    : "Error"}
                                </td>
                                <td
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                  className="btn"
                                  style={{
                                    backgroundColor: c.deadlineColor,
                                    color: "black",
                                    borderRadius: "10px",
                                    width: "100px",
                                    height: "30px",
                                    alignContent: "center",
                                    marginBlock: "30px",
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                >
                                  <span
                                    style={{
                                      display: "flex",
                                      marginTop: "-9px",
                                      marginLeft: "14px",
                                    }}
                                  >
                                    {c.deadline === null
                                      ? ""
                                      : `${c.deadline} päeva`}
                                  </span>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <span>{c.status_name}</span>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <span>
                                    {c.training == "1"
                                      ? "Jah"
                                      : c.training == "0"
                                      ? "Ei"
                                      : ""}
                                  </span>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <span>{c.required_candidates}</span>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <span>{c.job_applicats_count}</span>
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  {c.job_type}
                                </td>
                                <td
                                  style={{
                                    cursor: "pointer",
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                  onClick={() =>
                                    history.push({
                                      pathname: `/hr-jobs/view/${c.id}`,
                                      state: userPermission,
                                    })
                                  }
                                >
                                  <span
                                    className="d-inline-block text-truncate"
                                    style={{
                                      maxWidth: "200px",
                                    }}
                                  >
                                    {c.location}
                                  </span>
                                </td>
                                <td>
                                  <button
                                    type="button"
                                    className="btn btn-icon btn-sm"
                                    title="View"
                                    onClick={() =>
                                      history.push({
                                        pathname: `/hr-jobs/view/${c.id}`,
                                        state: userPermission,
                                      })
                                    }
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                  >
                                    <i className="fa fa-eye" />
                                  </button>
                                  {userPermission.includes(
                                    "Can-Delete-Jobs"
                                  ) ? (
                                    <button
                                      type="button"
                                      className="btn btn-icon btn-sm js-sweetalert"
                                      title="Delete"
                                      data-type="confirm"
                                      onClick={(e) => handleDelete(e, c.id)}
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "14px"
                                        }`,
                                      }}
                                    >
                                      <i className="fa fa-trash-o text-danger" />
                                    </button>
                                  ) : null}
                                  <button
                                    type="button"
                                    className="btn btn-icon btn-sm js-sweetalert"
                                    title="Duplicate"
                                    data-type="confirm"
                                    onClick={(e) => handleDuplicate(c.id)}
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                  >
                                    <i className="fa fa-clone"></i>
                                  </button>
                                </td>
                              </tr>
                            ))
                          ) : (
                            <tr>
                              <td>
                                <NoRecordFound />
                              </td>
                            </tr>
                          )
                        ) : (
                          <tr>
                            <td>
                              <DataLoader />
                            </td>
                          </tr>
                        )}
                      </tbody>
                    </Table>
                  </div>
                </CardBody>
                <Paginator
                  totalRecords={totalRecords}
                  pageLimit={10}
                  pageNeighbours={2}
                  setOffset={setOffset}
                  currentPage={pageNumber}
                  setCurrentPage={setPageNumber}
                />
              </Card>
            </div>
          </div>
        </Container>
      </div>
      <Toast />
    </>
  );
};

export default Jobs;
