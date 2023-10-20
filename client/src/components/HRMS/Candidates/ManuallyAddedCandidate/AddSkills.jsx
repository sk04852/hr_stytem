import React, { useEffect, useState } from "react";
import Multiselect from "multiselect-react-dropdown";
import { useDispatch, useSelector } from "react-redux";
import { addSkills, getSkills } from "../../../../redux/actions/skillsActions";
import { Form, Formik } from "formik";
import "../candidate.css";
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { Col, Row } from "reactstrap";

const Skills = ({ candidateCvID, cvData }) => {
  const dispatch = useDispatch();

  const initialValues = {
    candidatecv_id: candidateCvID,
    skill_ids: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [skills, setSkills] = useState([]);
  const [skillsById, setSkillsById] = useState([]);
  const [populateSkills, setPopulateSkills] = useState([]);

  useEffect(() => {
    const fetchSkills = async () => {
      try {
        const response = await dispatch(getSkills());
        setSkills(response.payload.data.Skills.data);

        if (cvData) {
          let cvSkills =
            cvData.ARVUTIOSKUS === undefined ? "" : cvData.ARVUTIOSKUS;

          if (cvSkills.length > 0) {
            for (let skill of cvSkills) {
              response.payload.data.Skills.data.find((item) => {
                if (item.name === skill) {
                  formValues.skill_ids.push(item.id);

                  let skills = [];
                  for (let i = 0; i < cvSkills.length; i++) {
                    skills.push(item);
                  }
                  setSkillsById(skills);
                }
              });
            }
          }
        } else {
          setFormValues(initialValues);
        }
      } catch (error) {
        throw error;
      }
    };
    fetchSkills();
  }, []);

  const skillsSelector = useSelector((state) => state.skills.skills.data);
  const skillsLoadingSelector = useSelector((state) => state.skills.isLoading);

  const onSelectSkills = (selectedList, selectedItem) => {
    setSkillsById(selectedList);
    console.log(selectedList);
  };

  const onRemoveSkills = (selectedList, selectedItem) => {
    setFormValues({ ...formValues, skill_ids: selectedList });
  };

  const handleSubmit = (values) => {
    for (let i = 0; i < skillsById.length; i++) {
      values["skill_ids"].push(skillsById[i]["id"]);
    }
    dispatch(addSkills(values));
  };

  return (
    <>
      <div className="card">
        <div className="card-header float-right">
          <h1>Oskused</h1>
        </div>

        <div className="card-body">
          <div className="row">
            <div className="col-sm-9">
              <Formik
                initialValues={formValues}
                enableReinitialize={true}
                onSubmit={(values) => handleSubmit(values)}
              >
                <Form>
                  <Row>
                    <Col sm={4} md={6}>
                      <Multiselect
                        options={skills} // Options to display in the dropdown
                        selectedValues={skillsById || []} // Preselected value to persist in dropdown
                        onSelect={onSelectSkills} // Function will trigger on select event
                        onRemove={onRemoveSkills} // Function will trigger on remove event
                        displayValue="name" // Property name to display in the dropdown options
                      />
                    </Col>
                  </Row>
                  <Row>
                    <div className={"w-100 d-flex justify-content-end"}>
                      <Col sm={3} className="mt-2">
                        <button
                          type={"submit"}
                          className="btn btn-primary float-right"
                          disabled={skillsLoadingSelector}
                        >
                          {skillsLoadingSelector ? <Loader /> : "Lisa oskus"}
                        </button>
                      </Col>
                    </div>
                  </Row>
                </Form>
              </Formik>
            </div>
          </div>
        </div>
      </div>
      <Toast />
    </>
  );
};

export default Skills;
