import React, { useEffect, useState } from "react";
import "./candidate.css";
import { Form, Formik } from "formik";
import Multiselect from "multiselect-react-dropdown";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import { useDispatch, useSelector } from "react-redux";
import { getSkills } from "../../../redux/actions/skillsActions";
import { updateCandidateSkills } from "../../../redux/actions/candidatesAction";

const Skills = ({ candidateCvId, candidateSkillsById }) => {
  const dispatch = useDispatch();

  const initialValues = {
    candidatecv_id: candidateCvId,
    skill_ids: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [skills, setSkills] = useState([]);
  const [skillsById, setSkillsById] = useState([]);

  useEffect(() => {
    dispatch(getSkills());
  }, []);

  const skillsSelector = useSelector((state) => state.skills.skills.data);
  const skillsLoadingSelector = useSelector(
    (state) => state.candidates.isLoading
  );

  const onSelectSkills = (selectedList, selectedItem) => {
    setSkillsById(selectedList);
  };

  const onRemoveSkills = (selectedList, selectedItem) => {
    setFormValues({ ...formValues, skill_ids: selectedList });
  };

  useEffect(() => {
    if (skillsSelector) {
      setSkills(skillsSelector.Skills.data);
    }
  }, [skillsSelector]);

  const handleSubmit = (values) => {
    for (let i = 0; i < skillsById.length; i++) {
      values["skill_ids"].push(skillsById[i]["id"]);
    }

    // values["candidatecv_id"] = candidateSkillsById[0].pivot.candidatecv_id;

    dispatch(updateCandidateSkills(values));
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
                onSubmit={(values) => handleSubmit(values)}
              >
                <Form>
                  <div className="row">
                    <Multiselect
                      options={skills} // Options to display in the dropdown
                      selectedValues={formValues.skills || []} // Preselected value to persist in dropdown
                      onSelect={onSelectSkills} // Function will trigger on select event
                      onRemove={onRemoveSkills} // Function will trigger on remove event
                      displayValue="name" // Property name to display in the dropdown options
                    />
                    <div className="col-sm-3 mt-2">
                      <button
                        type={"submit"}
                        className="btn btn-primary float-right"
                        disabled={skillsLoadingSelector}
                      >
                        {skillsLoadingSelector ? <Loader /> : "Uuenda oskus"}
                      </button>
                    </div>
                  </div>
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
