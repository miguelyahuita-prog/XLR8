"use client";

import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";

type Profile = {
  name: string;
  email: string;
  phone: string | null;
  address: string | null;
};

export function ProfileForm({ profile }: { profile: Profile }) {
  const router = useRouter();
  const [message, setMessage] = useState("");

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const response = await fetch("/api/profile", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(Object.fromEntries(form.entries()))
    });
    setMessage(response.ok ? "Perfil actualizado" : "No se pudo actualizar");
    router.refresh();
  }

  async function logout() {
    await fetch("/api/auth/logout", { method: "POST" });
    router.push("/");
    router.refresh();
  }

  return (
    <form className="card card-body" onSubmit={submit}>
      <label className="field"><span>Nombre</span><input name="name" defaultValue={profile.name} required /></label>
      <label className="field"><span>Email</span><input value={profile.email} disabled /></label>
      <label className="field"><span>Telefono</span><input name="phone" defaultValue={profile.phone ?? ""} /></label>
      <label className="field"><span>Direccion</span><input name="address" defaultValue={profile.address ?? ""} /></label>
      {message ? <p className="notice">{message}</p> : null}
      <div style={{ display: "flex", gap: 10, flexWrap: "wrap" }}>
        <button className="button">Guardar</button>
        <button className="button ghost" type="button" onClick={logout}>Salir</button>
      </div>
    </form>
  );
}
