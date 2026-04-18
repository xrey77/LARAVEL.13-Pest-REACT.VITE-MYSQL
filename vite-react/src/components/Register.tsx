import { useState } from 'react';
import axios from 'axios';

const api = axios.create({
  baseURL: "http://127.0.0.1:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json',
          }
})

export default function Register() {
  const [firstname, setFirstname] = useState<string>('');
  const [lastname, setLastname] = useState<string>('');
  const [email, setEmail] = useState<string>('');
  const [mobile, setMobile] = useState<string>('');
  const [username, setUsername] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [message, setMessage] = useState<string>('');

  const submitRegistration = (event: any) => {
    event.preventDefault();
    const data =JSON.stringify({ lastname: lastname, firstname: firstname,email: email, mobile: mobile,
      username: username, password: password });
    api.post("/api/register", data)
    .then((res: any) => {
          setMessage(res.data.message);
          window.setTimeout(() => {
            setMessage('');
          }, 3000);
      }, (error: any) => {
            if (error.response) {
              setMessage(error.response.data.message);
            } else {
              setMessage(error.message);
            }
            window.setTimeout(() => {
              setMessage('');
            }, 3000);
    });
    jQuery("#registerReset").trigger('click');
  }

  const CloseRegistration = (event: any) => {
    event.preventDefault();
    setFirstname('');
    setLastname('');
    setEmail('');
    setMobile('');
    setUsername('');
    setPassword('');
    setMessage('');
  }

  return (
    <div className="modal fade" id="staticRegister" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticRegisterLabel" aria-hidden="true">
      <div className="modal-dialog modal-dialog-centered">
        <div className="modal-content">
          <div className="modal-header bg-danger">
            <h1 className="modal-title fs-5 text-white" id="staticRegisterLabel">Account Registration</h1>
            <button onClick={CloseRegistration} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div className="modal-body">
            <form onSubmit={submitRegistration} autoComplete="off">
              <div className="row">
                <div className="col">
                  <div className="mb-3">
                    <input type="text" required value={firstname} onChange={e => setFirstname(e.target.value)} className="form-control border-secondary border-emboss" id="fname" placeholder="enter First Name"/>
                  </div>          
                </div>
                <div className="col">
                  <div className="mb-3">
                    <input type="text" required value={lastname} onChange={e => setLastname(e.target.value)} className="form-control border-secondary border-emboss" id="lname" placeholder="enter Last Name"/>
                  </div>          
                </div>
              </div>

              <div className="row">
                <div className="col">
                  <div className="mb-3">
                    <input type="emai" required value={email} onChange={e => setEmail(e.target.value)} className="form-control border-secondary border-emboss" id="email" placeholder="enter Email Address"/>
                  </div>          
                </div>
                <div className="col">
                  <div className="mb-3">
                    <input type="text" required value={mobile} onChange={e => setMobile(e.target.value)} className="form-control border-secondary border-emboss" id="mobile" placeholder="enter Mobile No."/>
                  </div>          
                </div>
              </div>

              <div className="row">
                <div className="col">
                  <div className="mb-3">
                    <input type="text" required value={username} onChange={e => setUsername(e.target.value)} className="form-control border-secondary border-emboss" id="uname" placeholder="enter Username"/>
                  </div>          
                </div>
                <div className="col">
                  <div className="mb-3">
                    <input type="password" required value={password} onChange={e => setPassword(e.target.value)} className="form-control border-secondary border-emboss" id="pword" placeholder="enter Password"/>
                  </div>          
                </div>
              </div>
              <div className="mb-3">
                <button type="submit" className="btn btn-danger mx-2">register</button>
                <button onClick={CloseRegistration} type="reset" className="btn btn-danger">reset</button>
              </div>
          </form>

          </div>
          <div className="modal-footer">
            <div className="w-100 text-center text-danger">{message}</div>
          </div>
        </div>
      </div>
    </div>            
  );  
}

