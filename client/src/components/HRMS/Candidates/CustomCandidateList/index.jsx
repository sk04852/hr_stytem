import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardFooter,
  Col,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import {
  candidateDeleteCustomList,
  candidateUpdateCustomListName,
} from "../../../../redux/actions/candidatesAction";
import { Field, Form, Formik } from "formik";
import { DataLoader, Loader } from "../../../constants/loaders";
import asterik from "../../../../assets/images/asterisk.png";
import { NoListFound } from "../../../constants/noRecordFound";
import { useTranslation } from "react-i18next";

const CustomCandidateList = (props) => {
  const {
    setCandidateListId,
    setTriggerUpdate,
    customListBgId,
    setCustomListBgId,
    setStatusCountNumber,
  } = props;
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const editCustomListNameInitialValues = {
    list_name_id: "",
    list_name: "",
  };

  const [editCustomListNameFormValues, setEditCustomListNameFormValues] =
    useState(editCustomListNameInitialValues);
  const [candidateLists, setCandidateLists] = useState([]);
  const [editCustomListNameModal, setEditCustomListNameModal] = useState(false);
  const [listBackgroundColor, setListBackgroundColor] = useState("");

  const toggleEditCustomListNameModal = () => {
    setEditCustomListNameModal(!editCustomListNameModal);
  };

  const customCandidateListSelector = useSelector(
    (state) => state.candidates.all_custom_list
  );
  const customCandidateListIsLoadingSelector = useSelector(
    (state) => state.candidates.isLoading
  );

  useEffect(() => {
    if (customCandidateListSelector) {
      setCandidateLists(customCandidateListSelector.candidate_list);
    }
  }, [customCandidateListSelector]);

  const handleDeleteFullList = async (id) => {
    const confirm = window.confirm("Oled kindel, et soovid listi kustutada?");
    if (confirm === true) {
      dispatch(
        candidateDeleteCustomList(id, setCandidateListId, setTriggerUpdate)
      );
    }
  };

  const handleEditListName = (values) => {
    dispatch(candidateUpdateCustomListName(values, setEditCustomListNameModal));
  };

  const renderEditListNameModal = () => {
    return (
      <Modal
        isOpen={editCustomListNameModal}
        toggle={toggleEditCustomListNameModal}
      >
        <ModalHeader toggle={toggleEditCustomListNameModal}>
          {t(
            "candidates.customCandidateList.editCandidateListName.editCandidateModalHeading"
          )}
        </ModalHeader>
        <Formik
          initialValues={editCustomListNameFormValues}
          enableReinitialize={true}
          onSubmit={(values) => handleEditListName(values)}
        >
          <Form>
            <fieldset disabled={customCandidateListIsLoadingSelector}>
              <ModalBody>
                <Label>
                  {t(
                    "candidates.customCandidateList.editCandidateListName.listName"
                  )}{" "}
                  <img src={asterik} height={10} width={10} className="mt-n2" />
                </Label>
                <Field
                  type={"text"}
                  className={"form-control"}
                  placeholder={"Sisesta uue listi nimetus"}
                  name={"list_name"}
                  required
                />
              </ModalBody>
              <ModalFooter>
                <Button
                  type={"submit"}
                  color="primary"
                  disabled={customCandidateListIsLoadingSelector}
                >
                  {customCandidateListIsLoadingSelector ? (
                    <Loader />
                  ) : (
                    <>{t("buttons.update")}</>
                  )}
                </Button>{" "}
                <Button
                  type={"button"}
                  color="secondary"
                  onClick={toggleEditCustomListNameModal}
                >
                  {t("buttons.close")}
                </Button>
              </ModalFooter>
            </fieldset>
          </Form>
        </Formik>
      </Modal>
    );
  };

  // const [customListBgId, setCustomListBgId] = useState("");

  const customListCards = (id) => {
    setCandidateListId(id);
    setCustomListBgId(id);
    setStatusCountNumber("");

    resetCandidateCountBackground();

    let bgColor = "#b5c0ca";
    setListBackgroundColor(bgColor);
  };

  const resetCandidateCountBackground = () => {
    let allCandidateCard = document.getElementById("candidate-phase-cards-1");
    let generalTabCard = document.getElementById("candidate-phase-cards-2");
    let shortlistedCard = document.getElementById("candidate-phase-cards-3");
    let firstInterviewCard = document.getElementById("candidate-phase-cards-4");
    let secondInterviewCard = document.getElementById(
      "candidate-phase-cards-5"
    );
    let offerCard = document.getElementById("candidate-phase-cards-6");
    let placedCard = document.getElementById("candidate-phase-cards-7");

    allCandidateCard.classList.remove("active-tab");
    generalTabCard.classList.remove("active-tab");
    shortlistedCard.classList.remove("active-tab");
    firstInterviewCard.classList.remove("active-tab");
    secondInterviewCard.classList.remove("active-tab");
    offerCard.classList.remove("active-tab");
    placedCard.classList.remove("active-tab");
  };

  const handleListDoubleClick = () => {
    setListBackgroundColor("");
    setCandidateListId("");
  };

  return (
    <>
      {renderEditListNameModal()}
      <Row>
        <div>
          <h5 className={"ml-3"}>
            <strong>{t("candidates.customCandidateListHeading")}</strong>
          </h5>
        </div>
      </Row>
      <Row>
        {candidateLists ? (
          <>
            {candidateLists.length > 0 ? (
              candidateLists.map((item, index) => (
                <Col
                  sm={4}
                  md={4}
                  key={index}
                  onClick={() => {
                    customListCards(item.id);
                  }}
                  onDoubleClick={handleListDoubleClick}
                >
                  <Card
                    id={"candidate-list-cards"}
                    style={{
                      backgroundColor: `${
                        customListBgId === item.id ? listBackgroundColor : ""
                      }`,
                    }}
                  >
                    <CardBody
                      id={"custom-candidate-id"}
                      className="w_sparkline hover_set ml-3"
                      data-toggle="collapse"
                      data-target={`#${item.list_name.replaceAll(" ", "-")}`}
                      aria-expanded="false"
                      aria-controls="collapseExample"
                    >
                      <div className="details w-100 d-flex flex-row justify-content-between">
                        <div>
                          <span>{item.list_name}</span>
                        </div>
                        <div>
                          <button
                            type="button"
                            className="btn btn-icon btn-sm"
                            title="Edit List Name"
                            onClick={() => {
                              setEditCustomListNameFormValues({
                                ...editCustomListNameFormValues,
                                list_name_id: item.id,
                                list_name: item.list_name,
                              });
                              setEditCustomListNameModal(true);
                            }}
                          >
                            <i className="fa fa-edit" />
                          </button>
                          <button
                            type="button"
                            className="btn btn-icon btn-sm js-sweetalert"
                            title="Delete"
                            data-type="confirm"
                            onClick={(event) => handleDeleteFullList(item.id)}
                          >
                            <i className="fa fa-trash-o text-danger" />
                          </button>
                        </div>
                        <h3 className="mb-0"></h3>
                      </div>
                      <div className="w_chart">
                        <div id="mini-bar-chart1" className="mini-bar-chart" />
                      </div>
                    </CardBody>
                  </Card>
                </Col>
              ))
            ) : (
              <NoListFound />
            )}
          </>
        ) : (
          <DataLoader />
        )}
      </Row>
    </>
  );
};

export default CustomCandidateList;
