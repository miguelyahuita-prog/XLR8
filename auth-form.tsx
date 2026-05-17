"use client";

import { useRouter, useSearchParams } from "next/navigation";
import { FormEvent, useState } from "react";

export function AuthForm({ mode }: { mode: "login" | "register" }) {
  const router = useRouter();
  const params = useSearchParams();
  const [error, setError] = useState("");

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError("");
    const form = new FormData(event.currentTarget);
    const body = Object.fromEntries(form.entries());
    const response = await fetch(`/api/auth/${mode}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body)
    });
    const payload = await response.json();
    if (!response.ok) {
      setError(payload.error ?? "No se pudo completar la accion");
      return;
    }
    router.push(params.get("next") ?? "/");
    router.refresh();
  }

  return (
    <form className="card card-body" onSubmit={submit}>
      {mode === "register" ? (
        <>
          <label className="field"><span>Nombre</span><input name="name" required /></label>
          <label className="field"><span>Telefono</span><input name="phone" /></label>
          <label className="field"><span>Direccion</span><input name="address" /></label>
        </>
      ) : null}
      <label className="field"><span>Email</span><input name="email" type="email" required /></label>
      <label className="field"><span>Contrasena</span><input name="password" type="password" required /></label>
      {error ? <p className="notice">{error}</p> : null}
      <button className="button">{mode === "login" ? "Ingresar" : "Crear cuenta"}</button>
    </form>
  );
}
