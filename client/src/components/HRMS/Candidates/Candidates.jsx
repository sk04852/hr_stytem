import React, { useCallback, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteCandidates,
  getAllCandidates,
  getCandidateCustomLists,
  candidateCreateCustomListMultiple,
  candidateRemoveCustomListMultiple,
  fetchBestMatchingJobs,
} from "../../../redux/actions/candidatesAction";
import { getCandidateActions } from "../../../redux/actions/candidateActions_Actions";
// import { getAllActiveJobs } from "../../../redux/actions/jobsAction";
import { useHistory } from "react-router-dom";
import {
  Alert,
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  DropdownToggle,
  FormGroup,
  Input,
  Label,
  Modal,
  ModalBody,
  ModalHeader,
  Row,
} from "reactstrap";
import Toast from "../../constants/toast";
import { DataLoader, Loader } from "../../constants/loaders";
import Paginator from "react-hooks-paginator";
import { Form, Formik } from "formik";
import CustomCandidateList from "./CustomCandidateList";
import asterik from "../../../assets/images/asterisk.png";
import { NoRecordFound } from "../../constants/noRecordFound";
import { FormValidationErrors } from "../../constants/errors";
import CreatableSelect from "react-select/creatable";
import BestJobMatch from "./BestJobMatch";
import { useTranslation } from "react-i18next";
import ChangeCandidatePhaseJobsModal from "./Modals/ChangeCandidatePhaseModal";
import CandidatePhaseCounts from "./CandidatePhaseCounts";

const Candidates = (props) => {
  const { fixNavbar } = props;
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const initialValues = {
    CV_ID: 2,
    mother_tongue: "",
    name: "",
    gender: "",
    description: "",
    source: "",
    location: "",
    job_history: "",
    education: "",
    lang_name: "",
    consent: true,
    files: "",
  };

  const customCandidateMultipleListInitialValues = {
    list_name_ids: [],
    list_name: [],
    candidate_ids: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [
    customCandidateMultipleListFormValues,
    setCustomCandidateMultipleListFormValues,
  ] = useState(customCandidateMultipleListInitialValues);
  const [candidateCustomList, setCandidateCustomList] = useState([]);
  const [candidateListId, setCandidateListId] = useState("");
  const [customCandidateListModal, setCustomCandidateListModal] =
    useState(false);
  const [listOptions, setListOptions] = useState([]);
  const [removeFromList, setRemoveFromList] = useState(false);
  const [customListTriggerUpdate, setCustomListTriggerUpdate] = useState(null);
  // CUSTOM LIST STATE END

  const [allCandidates, setAllCandidates] = useState([]);
  // const [genderFilter, setGenderFilter] = useState([]);
  // const [jobTypeFilter, setJobTypeFilter] = useState([]);
  // const [locationFilter, setLocationFilter] = useState([]);

  // PAGINATION STATES START
  const [offset, setOffset] = useState(0);
  const [pageNumber, setPageNumber] = useState(1);
  const [totalRecords, setTotalRecords] = useState(0);
  // PAGINATION STATES END
  const [userPermission, setUserPermission] = useState([]);
  // const [countdata, setCountData] = useState(0);
  // const [jobdata, setJobData] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  const [modal, setModal] = useState(false);
  const [jobModal, setJobModal] = useState(false);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [genderFilterValue, setGenderFilterValue] = useState(null);
  const [jobTypeFilterValue, setJobTypeFilterValue] = useState(null);
  const [locationFilterValue, setLocationFilterValue] = useState(null);
  const [statusCountNumber, setStatusCountNumber] = useState(null);
  const [errors, setErrors] = useState({});
  const [triggerUpdate, setTriggerUpdate] = useState(null);
  const [bestMatchData, setBestMatchData] = useState([]);
  const [sorting, setSorting] = useState(null);
  const [changePhaseModalIsOpen, setChangePhaseModalIsOpen] = useState(false);

  const [customListBgId, setCustomListBgId] = useState("");

  // CANDIDATE PHASE STATES
  const [phaseActionId, setPhaseActionId] = useState("");
  const [candidateId, setCandidateId] = useState("");
  const [actionTemplateKey, setActionTemplateKey] = useState("");
  const [phases, setPhases] = useState([]);

  const toggleCustomCandidateListModal = () => {
    setCustomCandidateListModal(!customCandidateListModal);
  };

  const toggleDropdown = () => {
    setDropdownOpen(!dropdownOpen);
  };

  const toggleChangeCandidatePhaseModal = async (
    actionId,
    candidateId,
    templateKey
  ) => {
    setPhaseActionId(actionId);
    setCandidateId(candidateId);
    setActionTemplateKey(templateKey);
    setChangePhaseModalIsOpen(!changePhaseModalIsOpen);
  };

  const toggleJobModal = (temp) => {
    setCandidateId(temp.id);
    setJobModal(!jobModal);
  };

  const handleFetchBestMatchingJobs = async (id) => {
    try {
      const response = await dispatch(fetchBestMatchingJobs(id));
      let matchingData = response.payload.data.hits;
      setBestMatchData(matchingData);
    } catch (error) {
      throw error;
    }
  };

  const toggleModal = () => {
    setModal(!modal);
  };

  const fetchCandiatePhases = async () => {
    try {
      const response = await dispatch(getCandidateActions());
      let actionsResponse = response.payload.Actions.data;
      let filteredActions = actionsResponse.filter((item) => {
        return item.id !== 8;
      });
      setPhases(filteredActions);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCandiatePhases();
  }, [triggerUpdate]);

  const fetchAllCandidates = async (gender, jobType, location) => {
    // let statusCount = candidateListId ? '' : statusCountNumber
    // if (candidateListId) {
    //   setStatusCountNumber("");
    // } else {
    //   setStatusCountNumber(statusCountNumber);
    // }
    try {
      const response = await dispatch(
        getAllCandidates(
          pageNumber,
          searchTerm,
          gender,
          jobType,
          location,
          statusCountNumber,
          candidateListId,
          sorting
        )
      );

      if (response.payload.data.length === 0) {
        setAllCandidates([]);
      } else {
        setAllCandidates(response.payload.data.data);
        setTotalRecords(response.payload.data.total);
        // setCountData(response.payload.data.statistics);

        // set filters
        // const genderFilter = [
        //   ...new Set(response.payload.data.data.map((item) => item.gender)),
        // ];
        // const jobTypeFilter = [
        //   ...new Set(response.payload.data.data.map((item) => item.job_type)),
        // ];
        // const locationFilter = [
        //   ...new Set(response.payload.data.data.map((item) => item.location)),
        // ];
        // setGenderFilter(genderFilter);
        // setJobTypeFilter(jobTypeFilter);
        // setLocationFilter(locationFilter);
      }
    } catch (error) {
      throw error;
    }
  };

  const fetchCandidateCustomList = async () => {
    try {
      const response = await dispatch(getCandidateCustomLists());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAllCandidates();
    localStorage.removeItem("candidate-personal-data-form");
  }, [
    pageNumber,
    candidateListId,
    searchTerm,
    statusCountNumber,
    triggerUpdate,
    sorting,
  ]);

  useEffect(() => {
    fetchCandidateCustomList();
  }, [triggerUpdate]);

  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const customListSelector = useSelector((state) => state.candidates);
  const candidateIsLoadingSelector = useSelector(
    (state) => state.candidates.isLoading
  );
  const settingsSelector = useSelector((state) => state.settings);

  const fetchCandidateCustomListData = async () => {
    try {
      if (customListSelector.all_custom_list) {
        setCandidateCustomList(customListSelector.all_custom_list);

        let customListOptions = [];
        if (
          customListSelector.all_custom_list.candidate_list &&
          customListSelector.all_custom_list.candidate_list.length > 0
        ) {
          customListSelector.all_custom_list.candidate_list.map((item) => {
            customListOptions.push({ value: item.id, label: item.list_name });
          });
          setListOptions(customListOptions);
        } else {
          setListOptions([]);
        }
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCandidateCustomListData();
  }, [customListSelector.all_custom_list, triggerUpdate]);

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getUserProfileSelector]);

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      setSortingOrder("dsc");
      setSorting("-" + e);
    }
    if (sortingOrder === "dsc") {
      setSortingOrder("asc");
      setSorting(e);
    }
  };

  const resetFilters = () => {
    let elements = document.getElementById("selectFilters");
    elements.selectedIndex = 0;
    let elements2 = document.getElementById("selectFilters1");
    elements2.selectedIndex = 0;
    let elements3 = document.getElementById("selectFilters2");
    elements3.value = "";

    setFormValues(initialValues);
    setGenderFilterValue(null);
    setJobTypeFilterValue(null);
    setLocationFilterValue(null);
    setStatusCountNumber(null);
    setCandidateListId("");

    fetchAllCandidates("", "", "");
  };

  let [candidateListIDs, setCandidateListIDs] = useState([]);

  const handleDelete = (e, id) => {
    if (id.length < 1) {
      window.confirm("Please Select Candidates You Want To Remove");
    } else {
      candidateListIDs = [id];
      const confirm = window.confirm("Are You Sure?");
      if (confirm === true) {
        dispatch(
          deleteCandidates(
            id,
            setCandidateListIDs,
            fetchAllCandidates,
            fetchCandiatePhases
          )
        );
      }
    }
  };

  const renderJobUpdateModal = () => {
    return (
      <Modal isOpen={jobModal} toggle={toggleJobModal}>
        <ModalHeader className={"modal-header"} toggle={toggleJobModal}>
          {t("candidates.candidateApplyForJob")}
        </ModalHeader>
        <ModalBody>
          <BestJobMatch
            bestMatchData={bestMatchData}
            candidateId={candidateId}
            setTriggerUpdate={setTriggerUpdate}
            handleFetchBestMatchingJobs={handleFetchBestMatchingJobs}
            offerPage={false}
          />
        </ModalBody>
      </Modal>
    );
  };

  const renderAddCandidateModal = () => {
    return (
      <Modal isOpen={modal} toggle={toggleModal}>
        <ModalHeader
          toggle={toggleModal}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "1.75rem"
                : settingsSelector.FontSize === "Extra Large"
                ? "2rem"
                : "1.25rem"
            }`,
          }}
        >
          {t(
            "candidates.addNewCandidate.addNewCandidateModal.addNewCandidateModalHeading"
          )}
        </ModalHeader>
        <ModalBody>
          <Row>
            <h6
              className={"w-100 text-center"}
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "large"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "x-large"
                    : "1rem"
                }`,
              }}
            >
              <strong>
                {t(
                  "candidates.addNewCandidate.addNewCandidateModal.addNewCandidateModalSubHeading"
                )}
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
                onClick={() => history.push("/hr-candidate/import-from-file")}
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
                {t(
                  "candidates.addNewCandidate.addNewCandidateModal.addNewCandidateImportFromCvButton"
                )}
              </Button>
              <Button
                type={"button"}
                color={"primary"}
                className={"p-3"}
                onClick={() => history.push("/hr-candidate/add-candidate")}
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
                {t(
                  "candidates.addNewCandidate.addNewCandidateModal.addNewCandidateAddManuallyButton"
                )}
              </Button>
            </div>
          </Row>
        </ModalBody>
      </Modal>
    );
  };

  const handleAddMultipleCustomListChange = (e) => {
    setCustomCandidateMultipleListFormValues({
      ...customCandidateMultipleListFormValues,
      list_name_ids: e,
    });
  };

  const handleAddMultipleCustomList = (value) => {
    let formData = new FormData();

    let newValues = [];

    if (customCandidateMultipleListFormValues.list_name_ids.length > 0) {
      customCandidateMultipleListFormValues.list_name_ids.map((item) => {
        if (typeof item.value === "string") {
          newValues.push({
            value: "",
            label: item.label,
          });
        }

        if (typeof item.value !== "string") {
          newValues.push({
            value: item.value,
            label: item.label,
          });
        }
      });
    }

    if (removeFromList === true) {
      if (newValues.length > 0) {
        newValues.map((item, index) => {
          formData.append(`list_name_ids[${index}]`, item.value);
        });
      }
    } else {
      if (newValues.length > 0) {
        newValues.map((item, index) => {
          formData.append(`custom_lists[${index}][id]`, item.value);
          formData.append(`custom_lists[${index}][name]`, item.label);
        });
      }
    }

    if (candidateListIDs.length > 0) {
      for (let i = 0; i < candidateListIDs.length; i++) {
        formData.append(`candidate_ids[${i}]`, candidateListIDs[i]);
      }
    }

    if (removeFromList === true) {
      dispatch(
        candidateRemoveCustomListMultiple(
          formData,
          setCandidateListIDs,
          setCustomCandidateListModal,
          setErrors,
          fetchAllCandidates
        )
      );
    } else {
      setRemoveFromList(false);
      dispatch(
        candidateCreateCustomListMultiple(
          formData,
          setCandidateListIDs,
          setCustomCandidateListModal,
          setErrors,
          fetchCandidateCustomList
        )
      );
    }
  };

  const renderCandidateListActionsModal = () => {
    return (
      <Modal
        isOpen={customCandidateListModal}
        toggle={toggleCustomCandidateListModal}
      >
        <ModalHeader
          toggle={() => {
            toggleCustomCandidateListModal();
            setRemoveFromList(false);
          }}
          className={"mb-2"}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "1.25rem"
            }`,
          }}
        >
          {t("candidates.addToListModal.headerText")}
        </ModalHeader>

        {/*ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*IF IDS ARE NULL*/}
        {candidateListIDs.length < 1 && (
          <Container fluid={true}>
            <Alert
              color={"primary"}
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "large"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "x-large"
                    : "0.9375rem"
                }`,
              }}
            >
              {t("candidates.alertText")}
            </Alert>
          </Container>
        )}

        <ModalBody>
          <Row>
            <Formik
              initialValues={customCandidateMultipleListFormValues}
              onSubmit={(values) => handleAddMultipleCustomList(values)}
            >
              <Form
                className={"w-100"}
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
                <fieldset disabled={candidateIsLoadingSelector}>
                  <FormGroup>
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
                      {t("candidates.addToListModal.listName")}{" "}
                      <img
                        src={asterik}
                        height={10}
                        width={10}
                        className="mt-n2"
                      />
                    </Label>
                    <CreatableSelect
                      isMulti
                      isSearchable
                      className="basic-multi-select"
                      name={"list_name_ids"}
                      closeMenuOnSelect={false}
                      onChange={(e) => handleAddMultipleCustomListChange(e)}
                      options={listOptions ? listOptions : []}
                      isDisabled={candidateListIDs.length < 1 ? true : false}
                    />
                  </FormGroup>
                  <FormGroup className={"w-100 d-flex justify-content-end"}>
                    <Button
                      type={"submit"}
                      color={"primary"}
                      disabled={
                        candidateIsLoadingSelector ||
                        candidateListIDs.length < 1
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
                      {candidateIsLoadingSelector ? (
                        <Loader />
                      ) : (
                        <>{t("buttons.save")}</>
                      )}
                    </Button>
                    <Button
                      type={"button"}
                      color={"danger"}
                      className={"ml-3"}
                      onClick={() => {
                        toggleCustomCandidateListModal();
                        setRemoveFromList(false);
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
                      {t("buttons.close")}
                    </Button>
                  </FormGroup>
                </fieldset>
              </Form>
            </Formik>
          </Row>
        </ModalBody>
      </Modal>
    );
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
    if (searchValue.length > 0) {
      setSearchTerm(searchValue);
    } else if (searchValue.length === 0) {
      setSearchTerm("");
    }
  };

  const optimizedSearchFn = useCallback(debounce(handleSearchChange), []);

  return (
    <>
      {renderAddCandidateModal()}
      {renderJobUpdateModal()}
      {renderCandidateListActionsModal()}
      <ChangeCandidatePhaseJobsModal
        toggleChangeCandidatePhaseModal={toggleChangeCandidatePhaseModal}
        changePhaseModalIsOpen={changePhaseModalIsOpen}
        phaseActionId={phaseActionId}
        actionTemplateKey={actionTemplateKey}
        candidateId={candidateId}
        triggerUpdate={triggerUpdate}
        setTriggerUpdate={setTriggerUpdate}
      />
      <div>
        <div className={`section-body ${fixNavbar ? "marginTop" : ""} `}>
          <Container fluid={true}>
            <div className="d-flex justify-content-end align-items-center mb-3">
              <div className="header-action w-100 text-right mt-2">
                {userPermission.includes("Can-Add-Candidate") ? (
                  <button
                    type="button"
                    className="btn btn-primary"
                    data-toggle="modal"
                    data-target="#exampleModal"
                    onClick={() => setModal(true)}
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
                    {t("candidates.addNewCandidateButton")}
                  </button>
                ) : null}
              </div>
            </div>
            <CandidatePhaseCounts
              phases={phases}
              setCandidateListId={setCandidateListId}
              setStatusCountNumber={setStatusCountNumber}
              setCustomListBgId={setCustomListBgId}
            />
            <CustomCandidateList
              setCandidateListId={setCandidateListId}
              setTriggerUpdate={setTriggerUpdate}
              setCustomListTriggerUpdate={setCustomListTriggerUpdate}
              customListBgId={customListBgId}
              setCustomListBgId={setCustomListBgId}
              setStatusCountNumber={setStatusCountNumber}
            />
            <Row className={"clearfix"}>
              <Col>
                <Card>
                  <CardBody>
                    <Row>
                      <Col lg={2} md={4} sm={6}>
                        <label>{t("candidates.candidateGender")}</label>
                        <div className="multiselect_div">
                          <select
                            className="custom-select"
                            id={"selectFilters"}
                            onChange={(event) =>
                              setGenderFilterValue(event.target.value)
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
                            <option value={""}>Ei ole valitud</option>
                            <option value={2}>Naine</option>
                            <option value={1}>Mees</option>
                          </select>
                        </div>
                      </Col>
                      <Col lg={2} md={4} sm={6}>
                        <label>{t("candidates.candidateJobType")}</label>
                        <div className="form-group">
                          <select
                            className="custom-select"
                            id={"selectFilters1"}
                            onChange={(event) =>
                              setJobTypeFilterValue(event.target.value)
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
                            <option value={""}>Ei ole valitud</option>
                            <option value={"1"}>
                              {t(
                                "candidates.bookmarks.personalInformation.fullTime"
                              )}
                            </option>
                            <option value={"2"}>
                              {t(
                                "candidates.bookmarks.personalInformation.partTime"
                              )}
                            </option>
                          </select>
                        </div>
                      </Col>
                      <Col lg={2} md={4} sm={6}>
                        <label>{t("candidates.candidateLocation")}</label>
                        <div className="form-group">
                          <Input
                            type={"text"}
                            className={"form-control"}
                            id={"selectFilters2"}
                            onChange={(event) =>
                              setLocationFilterValue(event.target.value)
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
                          />
                        </div>
                      </Col>
                      <Col lg={2} md={4} sm={6}>
                        <label>&nbsp;</label>
                        <button
                          type={"button"}
                          className="btn btn-sm btn-primary btn-block"
                          onClick={() =>
                            fetchAllCandidates(
                              genderFilterValue,
                              jobTypeFilterValue,
                              locationFilterValue
                            )
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
                          {t("buttons.filter")}
                        </button>
                      </Col>
                      <Col lg={2} md={4} sm={6}>
                        <label>&nbsp;</label>
                        <button
                          type={"reset"}
                          className="btn btn-sm btn-primary btn-block"
                          onClick={resetFilters}
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
                          {t("buttons.reset")}
                        </button>
                      </Col>
                    </Row>
                  </CardBody>
                </Card>
              </Col>
            </Row>
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
                              ? "x-large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "xx-large"
                              : "1.25rem"
                          }`,
                        }}
                      >
                        {/* {customList ? "Add Candidate" : "Candidate List"} */}
                        {t("candidates.candidateList")}
                      </h5>
                    </CardTitle>
                    <div className="card-options">
                      {userPermission.includes("Can-Candidate-Search") ? (
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
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
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
                  <Row>
                    <Col sm={12} md={12} className={"pl-4"}>
                      <Dropdown isOpen={dropdownOpen} toggle={toggleDropdown}>
                        <DropdownToggle
                          caret
                          style={{
                            background: "#5a5278",
                            borderColor: "#5a5278",
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
                        </DropdownToggle>
                        <DropdownMenu>
                          <DropdownItem
                            style={{
                              width: "-webkit-fill-available",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() => {
                              setRemoveFromList(false);
                              setCustomCandidateListModal(true);
                            }}
                          >
                            {t("candidates.addToList")}
                          </DropdownItem>
                          {candidateCustomList.candidate_list &&
                            candidateCustomList.candidate_list.length > 0 && (
                              <DropdownItem
                                style={{
                                  width: "-webkit-fill-available",
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "14px"
                                  }`,
                                }}
                                onClick={() => {
                                  setRemoveFromList(true);
                                  setCustomCandidateListModal(true);
                                }}
                              >
                                {t("candidates.deleteFromList")}
                              </DropdownItem>
                            )}
                          <DropdownItem
                            style={{
                              width: "-webkit-fill-available",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={(e) => handleDelete(e, candidateListIDs)}
                          >
                            {t("delete")}
                          </DropdownItem>
                        </DropdownMenu>
                      </Dropdown>
                    </Col>
                  </Row>
                  <CardBody>
                    <div className="table-responsive">
                      <table
                        id={"candidate-table"}
                        className="table table-hover table-striped table-vcenter text-nowrap mb-0 table-sortable"
                      >
                        <thead>
                          <tr>
                            <th className={"no-sort"}>#</th>
                            <th
                              className="no-sort"
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
                              {t("candidates.candidatePicture")}
                            </th>
                            <th
                              onClick={() => handleSorting("first_name")}
                              className={sortingOrder === "asc" ? "asc" : "dsc"}
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidateName")}
                            </th>

                            <th
                              className="no-sort"
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidatePhone")}
                            </th>
                            <th
                              className="no-sort"
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidateGender")}
                            </th>
                            {/* <th
                              onClick={() => handleSorting("action_name")}
                              className={sortingOrder === "asc" ? "asc" : "dsc"}
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidatePhase")}
                            </th> */}
                            <th
                              onClick={() => handleSorting("location")}
                              className={sortingOrder === "asc" ? "asc" : "dsc"}
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidateLocation")}
                            </th>
                            <th
                              className="no-sort"
                              style={{
                                paddingRight: "50px",
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
                              {t("candidates.candidateJobType")}
                            </th>
                            <th
                              className="no-sort"
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
                              {t("actions")}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          {!candidateIsLoadingSelector ? (
                            <>
                              {allCandidates !== undefined &&
                              allCandidates.length > 0 ? (
                                allCandidates.map((c, index) => (
                                  <tr key={index}>
                                    <td className="w40" id="candidate-checkbox">
                                      <label className="custom-control custom-checkbox">
                                        <input
                                          type="checkbox"
                                          className="custom-control-input"
                                          name="example-checkbox1"
                                          id="candidate-checkbox"
                                          onClick={() => {
                                            setCandidateListIDs([
                                              ...candidateListIDs,
                                              c.id,
                                            ]);
                                          }}
                                        />
                                        <span className="custom-control-label">
                                          &nbsp;
                                        </span>
                                      </label>
                                    </td>
                                    <td
                                      className="d-flex"
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
                                      style={{ cursor: "pointer" }}
                                    >
                                      <img
                                        src={c && c.photo}
                                        alt={"image"}
                                        className="avatar avatar-blue"
                                        data-toggle="tooltip"
                                        data-original-title="Avatar Name"
                                      />
                                    </td>
                                    <td
                                      style={{
                                        paddingRight: "50px",
                                        cursor: "pointer",
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "1rem"
                                        }`,
                                      }}
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
                                    >
                                      <h6
                                        className="mb-0"
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "1rem"
                                          }`,
                                        }}
                                      >
                                        {" "}
                                        {c && c.first_name} {c && c.last_name}
                                      </h6>
                                      <span>{c && c.email}</span>
                                    </td>
                                    <td
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
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
                                    >
                                      {c && c.phone}
                                    </td>
                                    <td
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
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
                                    >
                                      {c && c.gender_name}
                                    </td>
                                    {/* <td
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
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
                                    >
                                      <span
                                        className="d-inline-block text-truncate"
                                        style={{ maxWidth: "200px" }}
                                      >
                                        {c && c.action_name === null
                                          ? ""
                                          : c.action_name ===
                                            "General Candidates List"
                                          ? "Total Candidates List"
                                          : c.action_name}
                                      </span>
                                    </td> */}
                                    <td
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-candidate/view/${c.id}/1`,
                                          state: userPermission,
                                        })
                                      }
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
                                    >
                                      {c && c.location}
                                    </td>
                                    <td
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
                                      {c.job_type
                                        ? c.job_type
                                            .split(",")
                                            .map((jobType, index) => (
                                              <div key={index}>{jobType}</div>
                                            ))
                                        : ""}
                                    </td>
                                    <td>
                                      <button
                                        type="button"
                                        className="btn btn-icon btn-sm"
                                        title="View"
                                        onClick={() =>
                                          history.push({
                                            pathname: `/hr-candidate/view/${c.id}/1`,
                                            state: userPermission,
                                          })
                                        }
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
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
                                        "Can-Delete-Candidate"
                                      ) ? (
                                        <button
                                          type="button"
                                          className="btn btn-icon btn-sm js-sweetalert"
                                          title="Delete"
                                          data-type="confirm"
                                          onClick={(event) =>
                                            handleDelete(event, [c.id])
                                          }
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
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
                                      <div className="dropdown">
                                        <i
                                          className="fa fa-angle-down dropdown-toggle pt-2 pl-2"
                                          id="dropdownMenuButton"
                                          data-toggle="dropdown"
                                          aria-haspopup="true"
                                          aria-expanded="false"
                                          data-spy="scroll"
                                          data-boundary="window"
                                        />
                                        <div
                                          className="dropdown-menu"
                                          aria-labelledby="dropdownMenuButton"
                                        >
                                          <button
                                            type="button"
                                            className="close pr-3"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                          />
                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() => {
                                              toggleJobModal(c);
                                              handleFetchBestMatchingJobs(c.id);
                                            }}
                                          >
                                            {/*Apply For Job*/}
                                            {t(
                                              "candidates.candidateApplyForJob"
                                            )}
                                          </a>
                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                3,
                                                c.id
                                                // c.jobs[0].pivot.job_pr_id
                                              )
                                            }
                                            // onClick={(event) =>
                                            //   candidateApplyToJob(
                                            //     3,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id
                                            //   )
                                            // }
                                          >
                                            {/*Shortlisted*/}
                                            {t(
                                              "candidates.sendInterviewInformation"
                                            )}
                                          </a>
                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                4,
                                                c.id,
                                                "leping"
                                              )
                                            }
                                            // onClick={(event) =>
                                            //   candidateApplyToJob(
                                            //     4,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id,
                                            //     "leping"
                                            //   )
                                            // }
                                          >
                                            {/*1st interview*/}
                                            {t("candidates.1stInterview")}
                                          </a>
                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                5,
                                                c.id,
                                                "Töökuulutus"
                                              )
                                            }
                                            // onClick={(event) =>
                                            //   candidateApplyToJob(
                                            //     5,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id,
                                            //     "Töökuulutus"
                                            //   )
                                            // }
                                          >
                                            {/*2nd interview*/}
                                            {t("candidates.2ndInterview")}
                                          </a>

                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                6,
                                                c.id
                                              )
                                            }
                                            // onClick={(event) =>
                                            //   candidateApplyToJob(
                                            //     6,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id
                                            //   )
                                            // }
                                          >
                                            {/*make offer*/}
                                            {t("candidates.makeJobOffer")}
                                          </a>

                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                7,
                                                c.id,
                                                "Placed"
                                              )
                                            }
                                            // onClick={(event) =>
                                            //   candidateApplyToJob(
                                            //     7,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id,
                                            //     "Placed"
                                            //   )
                                            // }
                                          >
                                            {/*make placement*/}
                                            {t("candidates.hire")}
                                          </a>

                                          <a
                                            className="dropdown-item"
                                            style={{ cursor: "pointer" }}
                                            onClick={() =>
                                              toggleChangeCandidatePhaseModal(
                                                8,
                                                c.id,
                                                "Kutse skype töövestlusele RUS"
                                              )
                                            }
                                            // onClick={(event) => {
                                            //   candidateApplyToJob(
                                            //     8,
                                            //     c.id,
                                            //     c.jobs[0].pivot.job_pr_id,
                                            //     "Kutse skype töövestlusele RUS"
                                            //   );
                                            // }}
                                          >
                                            {/*rejected candidate*/}
                                            {t("candidates.reject")}
                                          </a>
                                        </div>
                                      </div>
                                    </td>
                                  </tr>
                                ))
                              ) : (
                                <tr>
                                  <td>
                                    <NoRecordFound />
                                  </td>
                                </tr>
                              )}
                            </>
                          ) : (
                            <tr>
                              <td>
                                <DataLoader />
                              </td>
                            </tr>
                          )}
                        </tbody>
                      </table>
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
      </div>
      <Toast />
    </>
  );
};

export default Candidates;
