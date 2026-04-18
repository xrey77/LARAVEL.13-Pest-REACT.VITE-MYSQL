import { useState, useEffect } from "react"
import jQuery from "jquery";
import axios from 'axios';

const api = axios.create({
   baseURL: "http://127.0.0.1:8000",
   headers: {'Accept': 'application/json',
             'Content-Type': 'application/json'}
})

export default function Mfa() {
  const [otp, setOtp] = useState<string>('');
  const [userid, setUserid] = useState<string>('');
  const [message, setMessage] = useState<string>('');
  const [token, setToken] = useState<string>('');
  const [isDisabled, setIsdisabled] = useState<boolean>(false);

  useEffect(() => {
    const uid = sessionStorage.getItem('USERID');
    if (uid !== null) {
      setUserid(uid);
    }
    const tokenz = sessionStorage.getItem('TOKEN');
    if (tokenz !== null) {
      setToken(tokenz);
    }

  },[]);

  const submitMfa = (event: React.ChangeEvent) => {
    event.preventDefault();
    setMessage('please wait..');      
    setIsdisabled(true);
    const jsonData = JSON.stringify({ otp: otp });
    api.patch(`/api/verifytotp/${userid}`, jsonData, { headers: {
      Authorization: `Bearer ${token}`
    }})
    .then((res) => {
            setMessage(res.data.message);
            sessionStorage.setItem("USERNAME", res.data.username);
            setTimeout(() => {
              setMessage('');
              setOtp('');
              setIsdisabled(false);
              jQuery("#mfaReset").trigger('click');              
              window.location.reload();
            }, 3000);
      }, (error: any) => {
            if (error.response) {
              setMessage(error.response.data.message);
            } else {
              setMessage(error.message);
            }
            setTimeout(() => {
              setMessage('');
              setIsdisabled(false);
              setOtp('');
            }, 3000);
            return;
    });              
  }

  const closeMfa = (event: any) => {
    event.preventDefault();
    setMessage('');
    setOtp('');
    sessionStorage.removeItem('USERID');
    sessionStorage.removeItem('USERNAME');
    sessionStorage.removeItem('USERPIC');
    sessionStorage.removeItem('TOKEN');
    location.reload();
  }

  return (
    <div className="modal fade" id="staticMfa" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticMfaLabel" aria-hidden="true">
      <div className="modal-dialog modal-sm modal-dialog-centered">
        <div className="modal-content">
          <div className="modal-header bg-info">
            <div className="modal-title fs-5 text-dark" id="staticMfaLabel">Multi-Factor Authenticator</div>
            <button onClick={closeMfa} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            <button id="mfaclose" type="button" className="btn-close d-none" data-bs-dismiss="modal" aria-label="Close"></button>

          </div>
          <div className="modal-body">
          <form onSubmit={submitMfa} autoComplete="off">
            <div className="mb-3">
              <input type="text" required value={otp} onChange={e => setOtp(e.target.value)} className="form-control border-dark" id="otp" placeholder="enter 6-digin OTP code" disabled={isDisabled}/>
            </div>          
            <div className="mb-3">
              <button type="submit" className="btn btn-info mx-2 text-dark" disabled={isDisabled}>submit</button>
              <button type="reset" className="btn btn-info text-dark">reset</button>
            </div>
          </form>            
          </div>
          <div className="modal-footer">
            <div className="w-100 text-center text-danger">{message}</div>
          </div>
        </div>
      </div>
    </div>    
  )
}
        
