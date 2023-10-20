import React, { useEffect, useState } from "react";
import { useHistory } from "react-router-dom";
import * as moment from "moment";
import { useDispatch, useSelector } from "react-redux";
import { Button, Modal, ModalBody, Row } from "reactstrap";
import {
  addJobs,
  deleteJobs,
  getAllJobs,
} from "../../../redux/actions/jobsAction";
import { getTokenItem } from "../../constants/token";
import Toast from "../../constants/toast";

const Jobs = (props) => {
  const { fixNavbar } = props;
  const dispatch = useDispatch();
  const history = useHistory();

  const [searchTerm, setSearchTerm] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");

  const initialValues = {
    title: "",
    status: "",
    deadline: "",
    company: "",
    required: "",
    benefits: "",
    location: "",
    Department: "",
    creator: "",
    contact: "",
    contactName: "",
    email: "",
    training: "",
    Observation: "",
    time_Period: "",
    job_salary: "",
    salary1: "",
    salary2: "",
    type_of_job: "",
    type_Of_Job_Comment: "",
    transport: "",
    transportComment: "",
    workingHours: "",
    workingHoursComment: "",
    clothes: "",
    clothesComment: "",
    Shifts: "",
    requirement: "",
    desiredlangcomment: "",
    work_lang_id: "",
    desired_lang_id: "",
    job_shifts_id: "",
    created_at: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [titleFilter, setTitleFilter] = useState([]);
  const [jobTypeFilter, setJobTypeFilter] = useState([]);
  const [companyFilter, setcompanyFilter] = useState([]);
  const [locationFilter, setLocationFilter] = useState([]);
  const [pageNumber, setPageNumber] = useState(1);
  const [date, setdate] = useState([]);
  const [data, setData] = useState([]);
  const [expiredate, setexpiredate] = useState(12);
  const [modal, setModal] = useState(false);
  const [userPermission, setUserPermission] = useState([]);


  
  const toggleModal = () => {
    setModal(!modal);
  };

  const inactiveSorting = (data) => {
    let upData = data.filter((u) => u.status == "1");
    let downData = data.filter((u) => u.status == "0" || u.status=="2");

    upData.push(...downData);
    setData(upData);
  };

  var currentDate = new Date();
  currentDate = moment(currentDate).format("YYYY-MM-DD HH:mm:ss");
 
  let updatedCurrentDate = currentDate.split(" ")[0].substring(8);
  
  for (let i = 0; i < data.length; i++) {
    if (data[i].deadline && typeof data[i].deadline === "string") {
      let updatedDate = data[i].deadline.split(" ")[0].substring(8);
     
      updatedDate = parseInt(updatedDate) - parseInt(updatedCurrentDate);
      
      if (updatedDate > 0) {
        data[i].deadline = updatedDate
        if (data[i].deadline <= 3 && data[i].deadline >= 0) {
          data[i] = {
            ...data[i],
            deadlineColor: "#E8769F",
          };
        } else if (data[i].deadline > 3 && data[i].deadline<10) {
          data[i] = {
            ...data[i],
            deadlineColor: "#FBBD08",
          };
        } else {
          console.log("in")
          data[i] = {
            ...data[i],
            deadlineColor: "#CEDD7A",
          };
        }
      } else {
        // console.log(data[i].deadline + "not okay")
        data[i].deadline = "0"
        data[i].status = "0";
        data[i] = {
          ...data[i],
          deadlineColor: "#E8769F",
        };
        
      }
    }
    }

  const jobsSelector = useSelector((state) => {
    return state.jobs;
  });
  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );

  useEffect(() => {
    dispatch(getAllJobs(pageNumber, searchTerm));
  }, [pageNumber, searchTerm]);

  const getJobsSelector = useSelector((state) => {
    return state.jobs.data.data.Jobs;
  });

  useEffect(() => {
    if (getJobsSelector) {
      setData(getJobsSelector.data);
    }

    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getJobsSelector, getUserProfileSelector]);

  useEffect(() => {
    if (getJobsSelector) {
      const titleFilter = [
        ...new Set(getJobsSelector.data.map((item) => item.title)),
      ];
      const jobTypeFilter = [
        ...new Set(getJobsSelector.data.map((item) => item.type_of_job)),
      ];
      const companyFilter = [
        ...new Set(getJobsSelector.data.map((item) => item.company)),
      ];
      const locationFilter = [
        ...new Set(getJobsSelector.data.map((item) => item.location)),
      ];
      setTitleFilter(titleFilter);
      setJobTypeFilter(jobTypeFilter);
      setcompanyFilter(companyFilter);
      setLocationFilter(locationFilter);
    }
  }, [getJobsSelector]);

  const handleSorting = (e) => {
    console.log(e);
    if (e == "status") {
      if (sortingOrder === "dsc") {
        inactiveSorting(data);
        setSortingOrder("asc");
      } else {
        let upData = data.filter((u) => u.status == "1");
        let downData = data.filter(
          (u) => u.status == "0" || u.status == "2"
        );
        console.log(downData)
        downData.push(...upData);
        setData(downData);
        setSortingOrder("dsc");
      }
    } else {
      if (sortingOrder === "asc") {
        const sorted = [...data]?.sort((a, b) =>
          a[e].toLowerCase() > b[e].toLowerCase() ? 1 : -1
        );
        setData(sorted);
        setSortingOrder("dsc");
      }
      if (sortingOrder === "dsc") {
        const sorted = [...data]?.sort((a, b) =>
          a[e].toLowerCase() < b[e].toLowerCase() ? 1 : -1
        );
        setData(sorted);
        setSortingOrder("asc");
      }
    }
  };

  const jobFilters = () => {
    let updatedItems = getJobsSelector.data.filter((curElem) => {
      return (
        curElem.title === formValues.title &&
        curElem.type_of_job === formValues.jobType &&
        curElem.company === formValues.company &&
        curElem.location === formValues.location
      );
    });
    setData(updatedItems);
  };

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
    setData(getJobsSelector.data);
  };

  const handleDelete = async (e, id) => {
    const confirm = window.confirm("Oled kindel?");
    if (confirm === true) {
      const thisClickedDelete = e.currentTarget;
      dispatch(deleteJobs(id, getTokenItem));
      thisClickedDelete.closest("tr").remove();
    }
  };

  const handleDuplicate = (id) => {
    // const duplicateRow = data[id];
    // const firstPart = data.slice(0, id + 1);
    // const secondPart = data.slice(id + 1, data.length);
    // const newData = [...firstPart, duplicateRow, ...secondPart];
    // document.getElementById("modalButton").click()
    // setDuplicaterow(newData)
    let newData = data.filter((u) => u.id == id);
    console.log(newData);
    dispatch(addJobs(...newData, getTokenItem));
    data.push(...newData);
    inactiveSorting(data);

    // setData(...data,newData);
  };

  // const handleSubmit = (values) => {
  //   let date = new Date();
  //   date = moment(date).format("YYYY-MM-DD HH:mm:ss");

  //   // values.creator = localStorage.getItem("name");
  //   values.status = "1";
  //   values.doc = "dummy";
  //   values.jobDescription = "dummy";
  //   values.divDescription = "dummy";
  //   values.comments = "dummy";
  //   values.benefits = "dummy";
  //   values.contact = "12345678900";
  //   values.created_at = date;
  //   console.log(values);
  //   dispatch(addJobs(values, getTokenItem));
  //   data.push(values);
  //   inactiveSorting(data);
  // };

  const formChange = (e) => {
    console.log(formValues);
    let name = e.target.name;
    let val = e.target.value;
    setFormValues({ ...formValues, [name]: val });
  };

  const renderAddCandidateModal = () => {
    return (
      <Modal isOpen={modal} toggle={toggleModal}>
        <div className={"modal-header"} toggle={toggleModal}>
          <h5 className="modal-title" id="exampleModalLabel">
            Lisa tööpakkumine
          </h5>
          <button
            type="button"
            className="close"
            data-dismiss="modal"
            aria-label="Close"
            onClick={toggleModal}
          >
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <ModalBody>
          <Row>
            <h6 className={"w-100 text-center"}>
              <strong>
                Kas soovid failist importida või käsitsi sisestada?
              </strong>
            </h6>
            <div
              className={
                "w-100 d-flex flex-row justify-content-center mt-3 mb-3"
              }
            >
              <Button
                type={"button"}
                color={"primary"}
                className={"mr-3 p-3"}
                onClick={() => alert("Still in development...")}
              >
                Import From file
              </Button>
              <Button
                type={"button"}
                color={"primary"}
                className={"p-3"}
                onClick={() => history.push("/hr-jobs/add-jobs")}
              >
                Add Jobs Manually
              </Button>
            </div>
          </Row>
        </ModalBody>
      </Modal>
    );
  };

  return (
    <>
      {renderAddCandidateModal()}
      <div>
        <div>
          <div className={`section-body ${fixNavbar ? "marginTop" : ""} `}>
            <div className="container-fluid">
              <div className="d-flex justify-content-between align-items-center mb-3">
                {userPermission.includes("Can-Add-Jobs") ? (
                  <div className="header-action w-100 text-right mt-2">
                    <button
                      type="button"
                      className="btn btn-primary"
                      data-toggle="modal"
                      onClick={() => setModal(true)}
                    >
                      <i className="fe fe-plus mr-2" />
                      Lisa uus tööpakkumine
                    </button>
                  </div>
                ) : null}
              </div>
              <div className={"row clearfix"}>
                <div className="col-12">
                  <div className="card">
                    <div className="card-body">
                      <div className="row">
                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>Nimi</label>
                          <div className="multiselect_div">
                            <select
                              className="custom-select"
                              id={"selectFilters"}
                              onChange={(event) =>
                                setFormValues({
                                  ...formValues,
                                  title: event.target.value,
                                })
                              }
                            >
                              <option value={0}>Ei ole valitud</option>
                              {titleFilter.map((item, index) => (
                                <option value={item} key={index}>
                                  {item}
                                </option>
                              ))}
                            </select>
                          </div>
                        </div>
                        {/*<div className="col-lg-2 col-md-4 col-sm-6">*/}
                        {/*  <label>Status</label>*/}
                        {/*  <div className="form-group">*/}
                        {/*    <select*/}
                        {/*      className="custom-select"*/}
                        {/*      id={"selectFilters1"}*/}
                        {/*      onChange={(event) =>*/}
                        {/*        setFormValues({*/}
                        {/*          ...formValues,*/}
                        {/*          status: event.target.value,*/}
                        {/*        })*/}
                        {/*      }*/}
                        {/*    >*/}
                        {/*      <option value={0}>None Selected</option>*/}
                        {/*      {statusFilter.map((item, index) => (*/}
                        {/*        <option value={item} key={index}>*/}
                        {/*          {item}*/}
                        {/*        </option>*/}
                        {/*      ))}*/}
                        {/*    </select>*/}
                        {/*  </div>*/}
                        {/*</div>*/}
                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>Töö liik</label>
                          <div className="form-group">
                            <select
                              className="custom-select"
                              id={"selectFilters1"}
                              onChange={(event) =>
                                setFormValues({
                                  ...formValues,
                                  jobType: event.target.value,
                                })
                              }
                            >
                              <option value={0}>Ei ole valitud</option>
                              {jobTypeFilter.map((item, index) => (
                                <option value={item} key={index}>
                                  {item}
                                </option>
                              ))}
                            </select>
                          </div>
                        </div>

                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>company</label>
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
                            >
                              <option value={0}>None Selected</option>
                              {companyFilter.map((item, index) => (
                                <option value={item} key={index}>
                                  {item}
                                </option>
                              ))}
                            </select>
                          </div>
                        </div>
                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>Location</label>
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
                            >
                              <option value={0}>None Selected</option>
                              {locationFilter.map((item, index) => (
                                <option value={item} key={index}>
                                  {item}
                                </option>
                              ))}
                            </select>
                          </div>
                        </div>
                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>&nbsp;</label>
                          <button
                            type={"button"}
                            className="btn btn-sm btn-primary btn-block"
                            onClick={() =>
                              jobFilters(
                                formValues.title,
                                formValues.jobType,
                                formValues.company,
                                formValues.location
                              )
                            }
                          >
                            Filter
                          </button>
                        </div>
                        <div className="col-lg-2 col-md-4 col-sm-6">
                          <label>&nbsp;</label>
                          <button
                            type={"reset"}
                            className="btn btn-sm btn-primary btn-block"
                            onClick={resetFilters}
                          >
                            Reset
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="section-body">
            <div className="container-fluid">
              <div className="tab-content">
                <div
                  className="tab-pane fade show active"
                  id="Employee-list"
                  role="tabpanel"
                >
                  <div className="card">
                    <div className="card-header">
                      <h3 className="card-title">Jobs List</h3>
                      <div className="card-options">
                        {userPermission.includes("Can-Job-Search") ? (
                          <form>
                            <div className="input-group">
                              <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Search something..."
                                name="s"
                                onChange={(event) =>
                                  setSearchTerm(event.target.value)
                                }
                              />
                            </div>
                          </form>
                        ) : null}
                      </div>
                    </div>
                    <div className="card-body">
                      <div
                        className="table-responsive"
                        style={{ overflowX: "auto" }}
                      >
                        <table
                          id={"candidate-table"}
                          className="table table-hover table-striped table-vcenter mx-2 text-sm text-nowrap mb-0 table-sortable"
                          style={{ marginLeft: "-1.25rem" }}
                        >
                          <thead>
                            <tr>
                              <p
                                className={"text-center mb-0"}
                                style={{ color: "#000", marginTop: "5px" }}
                              >
                                #
                              </p>
                              <th
                                onClick={() => handleSorting("title")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                TITLE
                              </th>
                              <th
                                onClick={() => handleSorting("company")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                Company
                              </th>
                              <th
                                onClick={() => handleSorting("created_at")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                Date Created
                              </th>
                              <th
                                onClick={() => handleSorting("deadline")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                Deadline
                              </th>
                              <th
                                onClick={() => handleSorting("status")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                STATUS
                              </th>
                              <th
                                onClick={() => handleSorting("training")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                Training
                              </th>
                              <th
                                onClick={() => handleSorting("required")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                required
                              </th>
                              <th
                                // onClick={() => handleSorting("applied")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                Applied
                              </th>
                              <th
                                onClick={() => handleSorting("type_of_job")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                JOB TYPE
                              </th>
                              {/* <th
                                onClick={() => handleSorting("company")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                company
                              </th> */}
                              <th
                                onClick={() => handleSorting("location")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                LOCATION
                              </th>
                              {/* <th
                                onClick={() => handleSorting("contact")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "0px" }}
                              >
                                CONTACT
                              </th>
                              <th
                                onClick={() => handleSorting("doc")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "5px" }}
                              >
                                DOC
                              </th>
                              <th
                                onClick={() => handleSorting("jobDescription")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                JOB DESCRIPTION
                              </th>
                              <th
                                onClick={() => handleSorting("divDescription")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                DIV DESCRIPTION
                              </th>
                              <th
                                onClick={() => handleSorting("comments")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                COMMENTS
                              </th>
                              <th
                                onClick={() => handleSorting("benefits")}
                                className={
                                  sortingOrder === "asc" ? "asc" : "dsc"
                                }
                                style={{ paddingRight: "25px" }}
                              >
                                BENEFITS
                              </th> */}
                              <th>ACTION</th>
                            </tr>
                          </thead>
                          <tbody>
                            {data
                              ? data
                                  .filter((c) => {
                                    if (searchTerm === "") {
                                      return c;
                                    } else if (
                                      c.title
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                    ) {
                                      return c;
                                    } else if (
                                      c.type_of_job
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                    ) {
                                      return c;
                                    } else if (
                                      c.company
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                    ) {
                                      return c;
                                    } else if (
                                      c.location
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                    ) {
                                      return c;
                                    }
                                  })
                                  .map((c, index) => (
                                    <tr key={index}>
                                      <td className="w40">
                                        <label className="custom-control custom-checkbox">
                                          <input
                                            type="checkbox"
                                            className="custom-control-input"
                                            name="example-checkbox1"
                                            defaultValue="option1"
                                          />
                                          <span className="custom-control-label">
                                            &nbsp;
                                          </span>
                                        </label>
                                      </td>
                                      <td>
                                        <h6 className="mb-0">{c.title}</h6>
                                      </td>
                                      <td>{c.company}</td>
                                      <td>
                                        {c.created_at != null
                                          ? c.created_at.split(" ")[0]
                                          : "Error"}
                                      </td>
                                      <td
                                        className="btn"
                                        style={{
                                          backgroundColor: c.deadlineColor,
                                          color: "black",
                                          borderRadius: "10px",
                                          // textAlign: "center",
                                          width: "100px",
                                          height: "30px",
                                          alignContent: "center",
                                          marginBlock: "30px",
                                        }}
                                      >
                                        <span
                                          style={{
                                            display: "flex",
                                            marginTop: "-9px",
                                            marginLeft: "14px",
                                          }}
                                        >
                                          {c.deadline} days
                                        </span>
                                      </td>
                                      <td>
                                        <span>
                                          {c.status == "1"
                                            ? "Ootel"
                                            : c.status == "2"
                                            ? "Aktiivne"
                                            : "Mitteaktiivne"}
                                        </span>
                                      </td>
                                      <td>
                                        <span>{c.training}</span>
                                      </td>
                                      <td>
                                        <span>{c.required}</span>
                                      </td>
                                      <td>
                                        <span>0</span>
                                      </td>
                                      <td>{c.type_of_job ?
                                        c.type_of_job.split(" ")[0] == "According" ?
                                        "According to the Contract":c.type_of_job:""
                                      }</td>
                                      {/* <td>{c.company}</td> */}
                                      <td>{c.location}</td>
                                      {/* <td>{c.contact}</td>
                                      <td>
                                        <span
                                          className="d-inline-block text-truncate"
                                          style={{
                                            maxWidth: "150px",
                                          }}
                                        >
                                          {c.doc}
                                        </span>
                                      </td>
                                      <td>
                                        <span
                                          className="d-inline-block text-truncate"
                                          style={{
                                            maxWidth: "150px",
                                          }}
                                        >
                                          {c.jobDescription}
                                        </span>
                                      </td>
                                      <td>
                                        <span
                                          className="d-inline-block text-truncate"
                                          style={{
                                            maxWidth: "150px",
                                          }}
                                        >
                                          {c.divDescription}
                                        </span>
                                      </td>
                                      <td>
                                        <span
                                          className="d-inline-block text-truncate"
                                          style={{
                                            maxWidth: "150px",
                                          }}
                                        >
                                          {c.comments}
                                        </span>
                                      </td>
                                      <td>
                                        <span
                                          className="d-inline-block text-truncate"
                                          style={{
                                            maxWidth: "150px",
                                          }}
                                        >
                                          {c.benefits}
                                        </span>
                                      </td> */}
                                      <td>
                                        {/*<Link to={`/hr-jobs/view/${c.id}`}>*/}
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
                                        >
                                          <i className="fa fa-eye" />
                                        </button>
                                        {/*</Link>*/}
                                        {/*<Link to={`/hr-jobs/edit/${c.id}`}>*/}
                                        {/*  <button*/}
                                        {/*      type="button"*/}
                                        {/*      className="btn btn-icon btn-sm"*/}
                                        {/*      title="Edit"*/}
                                        {/*      // onClick={() =>*/}
                                        {/*      //   this.handleEdit(c.id)*/}
                                        {/*      // }*/}
                                        {/*  >*/}
                                        {/*    <i className="fa fa-edit" />*/}
                                        {/*  </button>*/}
                                        {/*</Link>*/}
                                        {userPermission.includes(
                                          "Can-Delete-Jobs"
                                        ) ? (
                                          <button
                                            type="button"
                                            className="btn btn-icon btn-sm js-sweetalert"
                                            title="Delete"
                                            data-type="confirm"
                                            onClick={(e) =>
                                              handleDelete(e, c.id)
                                            }
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
                                        >
                                          <i className="fa fa-clone"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  ))
                              : "Loading..."}
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        className="modal fade"
        id="exampleModal"
        tabIndex={-1}
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title" id="exampleModalLabel">
                Add Job
              </h5>
              <button
                type="button"
                className="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">×</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      {/* <JobsActivity job={data} /> */}
      <Toast />
    </>
  );
};

export default Jobs;
